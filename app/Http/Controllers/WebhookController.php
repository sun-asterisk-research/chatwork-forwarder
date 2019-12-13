<?php

namespace App\Http\Controllers;

use App\Enums\WebhookStatus;
use App\Http\Requests\WebhookCreateRequest;
use App\Http\Requests\WebhookUpdateRequest;
use App\Models\Bot;
use App\Models\Webhook;
use App\Repositories\Interfaces\WebhookRepositoryInterface as WebhookRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Auth;

class WebhookController extends Controller
{
    private $webhookRepository;

    public function __construct(WebhookRepository $webhookRepository)
    {
        $this->webhookRepository = $webhookRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = config('paginate.perPage');
        $webhooks = $this->webhookRepository->getAllByUser($perPage);

        return view('webhooks.index', compact('webhooks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $webhookStatuses = array_change_key_case(WebhookStatus::toArray());
        $bots = Bot::all();

        return view('webhooks.create', compact('webhookStatuses', 'bots'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WebhookCreateRequest $request)
    {
        $data = $request->except('_token');
        $data['token'] = md5(Auth::id() . '' . time());
        $data['user_id'] = Auth::id();

        try {
            $webhook = $this->webhookRepository->create($data);

            return redirect()->route('webhooks.edit', $webhook)
                             ->with('messageSuccess', 'This webhook successfully created');
        } catch (QueryException $exception) {
            return redirect()->back()->with('messageFail', 'Create failed. Something went wrong')->withInput();
        }
    }

    /**\
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function edit(Webhook $webhook)
    {
        $this->authorize('update', $webhook);
        $payloads = $webhook->payloads()->get();
        $webhookStatuses = array_change_key_case(WebhookStatus::toArray());
        $bots = Bot::all();

        return view('webhooks.edit', compact('webhook', 'payloads', 'webhookStatuses', 'bots'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function update(WebhookUpdateRequest $request, Webhook $webhook)
    {
        $this->authorize('update', $webhook);
        $data = $request->only(['name', 'status', 'description', 'bot_id', 'room_name', 'room_id']);

        try {
            $webhook = $this->webhookRepository->update($webhook->id, $data);
            return redirect()->route('webhooks.edit', $webhook)
                             ->with('messageSuccess', 'This webhook successfully updated');
        } catch (QueryException $exception) {
            return redirect()->back()->with('messageFail', 'Update failed. Something went wrong')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function destroy(Webhook $webhook)
    {
        $this->authorize('delete', $webhook);

        try {
            $this->webhookRepository->delete($webhook->id);
            return redirect('/webhooks')->with('messageSuccess', 'This webhook successfully deleted');
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', 'Delete failed. Something went wrong');
        }
    }

    public function changeStatus(Request $request)
    {
        $webhook = $this->webhookRepository->find($request->id);
        $this->authorize('update', $webhook);

        if ($request->status == WebhookStatus::ENABLED()->key) {
            $status = WebhookStatus::ENABLED;
        } else {
            $status = WebhookStatus::DISABLED;
        }

        $result = $this->webhookRepository->update($request->id, ['status' => $status]);

        if ($result) {
            return 'This webhook was updated successfully';
        }

        return 'Updated failed. Something went wrong';
    }
}
