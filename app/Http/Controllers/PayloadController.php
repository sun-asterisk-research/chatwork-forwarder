<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayloadCreateRequest;
use App\Models\Condition;
use App\Models\Payload;
use App\Models\Webhook;
use App\Repositories\Interfaces\PayloadRepositoryInterface as PayloadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayloadController extends Controller
{
    private $payloadRepository;

    public function __construct(PayloadRepository $payloadRepository)
    {
        $this->payloadRepository = $payloadRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($webhookId)
    {
        return view('payloads.create', compact('webhookId'));
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
        $data = $request->only(['content']);
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
            $request->session()->flash('messageSuccess', 'This payload successfully created');

            return $payload->id;
        } catch (QueryException $exception) {
            DB::rollBack();
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
    public function update(Request $request, Webhook $webhook, Payload $payload)
    {
        $conditions = $request->only('conditions');
        $data = $request->only('content');
        $ids = (array) $request->ids;
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
            $request->session()->flash('messageSuccess', 'This payload successfully updated');

            return $payload->id;
        } catch (QueryException $exception) {
            DB::rollBack();
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
        try {
            $this->payloadRepository->delete($payload->id);

            return redirect()->route('webhooks.edit', $webhook)
                             ->with('messageSuccess', 'This payload successfully deleted');
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', 'Delete failed. Something went wrong');
        }
    }
}
