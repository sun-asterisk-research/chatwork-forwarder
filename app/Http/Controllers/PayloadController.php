<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayloadCreateRequest;
use App\Http\Requests\PayloadUpdateRequest;
use App\Models\Condition;
use App\Models\Payload;
use App\Models\Webhook;
use App\Repositories\Interfaces\PayloadRepositoryInterface as PayloadRepository;
use App\Repositories\Interfaces\TemplateRepositoryInterface as TemplateRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;

class PayloadController extends Controller
{
    private $payloadRepository;
    private $templateRepository;

    public function __construct(PayloadRepository $payloadRepository, TemplateRepository $templateRepository)
    {
        $this->payloadRepository = $payloadRepository;
        $this->templateRepository = $templateRepository;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Webhook $webhook)
    {
        $this->authorize('update', $webhook);
        $templates = $this->templateRepository->getTemplate();

        return view('payloads.create', compact('webhook', 'templates'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PayloadCreateRequest $request, $webhookId)
    {
        $webhook = Webhook::findOrFail($webhookId);
        $this->authorize('update', $webhook);

        $data = $request->only(['content_type', 'content', 'params']);
        $data['webhook_id'] = $webhook->id;

        DB::beginTransaction();
        try {
            $payload = $this->payloadRepository->create($data);
            $conditions = $request->only('fields', 'operators', 'values');
            if ($conditions) {
                for ($i = 0; $i < count($conditions['fields']); $i++) {
                    $field = trim($conditions['fields'][$i]);
                    $operator = trim($conditions['operators'][$i]);
                    $value = trim($conditions['values'][$i]);

                    if (!empty($field) && !empty($operator) && !empty($value)) {
                        $payload->conditions()->create([
                            'field' => $field,
                            'operator' => $operator,
                            'value' => $value,
                        ]);
                    }
                }
            }

            DB::commit();
            $request->session()->flash('messageSuccess', [
                'status' => 'Create success',
                'message' => 'This payload successfully created',
            ]);

            return $payload->id;
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('messageFail', [
                'status' => 'Create failed',
                'message' => 'Create failed. Something went wrong',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payload  $payload
     * @return \Illuminate\Http\Response
     */
    public function edit(Webhook $webhook, Payload $payload)
    {
        $this->authorize('update', [$payload, $webhook]);

        $conditions = $payload->conditions()->get();
        return view('payloads.edit', compact('payload', 'webhook', 'conditions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payload  $payload
     * @return \Illuminate\Http\Response
     */
    public function update(PayloadUpdateRequest $request, Webhook $webhook, Payload $payload)
    {
        $this->authorize('update', [$payload, $webhook]);
        $conditions = $request->only('conditions');
        $data = $request->only(['content_type', 'content', 'params']);
        $ids = (array)$request->ids;
        DB::beginTransaction();
        try {
            $payload = $this->payloadRepository->update($payload->id, $data);
            $payload->conditions()->whereNotIn('id', $ids)->delete();
            if ($conditions) {
                foreach ($conditions['conditions'] as $condition) {
                    if ($condition['id']) {
                        Condition::whereId($condition['id'])->update($condition);
                    } else {
                        $payload->conditions()->create($condition);
                    }
                }
            }

            DB::commit();
            $request->session()->flash('messageSuccess', [
                'status' => 'Update success',
                'message' => 'This payload successfully updated',
            ]);

            return $payload->id;
        } catch (Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with('messageFail', [
                'status' => 'Update failed',
                'message' => 'Update failed. Something went wrong',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payload  $payload
     * @return \Illuminate\Http\Response
     */
    public function destroy(Webhook $webhook, Payload $payload)
    {
        $this->authorize('delete', [$payload, $webhook]);
        if ($payload->conditions->count() > 0) {
            return redirect()->back()
                ->with('messageFail', [
                    'status' => 'Delete failed',
                    'message' => 'This payload has some conditions to be related with, please delete them first',
                ]);
        }

        try {
            $this->payloadRepository->delete($payload->id);

            return redirect()->route('webhooks.edit', $webhook)
                ->with('messageSuccess', [
                    'status' => 'Delete success',
                    'message' => 'This payload successfully deleted',
                ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => 'Delete failed. Something went wrong',
            ]);
        }
    }

    public function copy(Webhook $webhook, Payload $payload)
    {
        $this->authorize('update', [$payload, $webhook]);

        $conditions = $payload->conditions()->get();
        $templates = $this->templateRepository->getTemplate();
        return view('payloads.copy', compact('payload', 'webhook', 'conditions', 'templates'));
    }
}
