<?php

namespace App\Http\Controllers;

use App\Enums\WebhookStatus;
use App\Http\Requests\WebhookCreateRequest;
use App\Http\Requests\WebhookUpdateRequest;
use App\Models\Bot;
use App\Models\Webhook;
use App\Repositories\Interfaces\WebhookRepositoryInterface as WebhookRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Auth;
use Exception;

class WebhookController extends Controller
{
    private $webhookRepository;
    private $userRepository;

    public function __construct(
        WebhookRepository $webhookRepository,
        UserRepository $userRepository
    ) {
        $this->webhookRepository = $webhookRepository;
        $this->userRepository = $userRepository;
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
        if ($webhooks->count() == 0 && $webhooks->previousPageUrl()) {
            return redirect($webhooks->previousPageUrl());
        } else {
            return view('webhooks.index', compact('webhooks'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $bots = Bot::byUser(Auth::id());

        return view('webhooks.create', compact('bots'));
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
            if (isset($data['use_default']) && $data['use_default'] === Bot::USE_DEFAULT_SLACK_BOT) {
                $data['bot_id'] = config('slack.slack_bot_id');
            }
            $webhook = $this->webhookRepository->create($data);

            return redirect()->route('webhooks.edit', $webhook)
                ->with('messageSuccess', [
                    'status' => 'Create success',
                    'message' => 'This webhook successfully created',
                ]);
        } catch (QueryException $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Create failed',
                'message' => 'Create failed. Something went wrong',
            ])->withInput();
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
        $mappings = $webhook->mappings()->get();
        $bots = Bot::byUser($webhook->user_id);

        return view('webhooks.edit', compact('webhook', 'payloads', 'bots', 'mappings'));
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
        if ($request->input('use_default') === Bot::USE_DEFAULT_SLACK_BOT) {
            $data['bot_id'] = config('slack.slack_bot_id');
        }

        try {
            if ($request->email) {
                $user = $this->userRepository->findByEmail($request->email);
                $data['user_id'] = $user ? $user->id : null;
                $webhook = $this->webhookRepository->update($webhook->id, $data);
                return redirect()->route('webhooks.index', $webhook)
                    ->with('messageSuccess', [
                        'status' => 'Update success',
                        'message' => 'This webhook successfully updated',
                    ]);
            } else {
                $webhook = $this->webhookRepository->update($webhook->id, $data);

                return redirect()->route('webhooks.edit', $webhook)
                    ->with('messageSuccess', [
                        'status' => 'Update success',
                        'message' => 'This webhook successfully updated',
                    ]);
            }
        } catch (QueryException $exception) {
            logger($exception);
            return redirect()->back()->with('messageFail', [
                'status' => 'Update failed',
                'message' => 'Update failed. Something went wrong',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Webhook $webhook)
    {
        $page = $request->page ? ['page' => $request->page] : null;
        $this->authorize('delete', $webhook);
        if ($webhook->payloads->count() > 0) {
            return redirect()->back()
                ->with('messageFail', [
                    'status' => 'Delete failed',
                    'message' => 'This webhook has some payloads to be related with, please delete them first',
                ]);
        }

        if ($webhook->mappings->count() > 0) {
            return redirect()->back()
                ->with('messageFail', [
                    'status' => 'Delete failed',
                    'message' => 'This webhook has some mappings to be related with, please delete them first',
                ]);
        }

        if ($webhook->payloadHistories->count() > 0) {
            return redirect()->back()
                ->with('messageFail', [
                    'status' => 'Delete failed',
                    'message' => 'This webhook has some payload histories to be related with, please delete them first',
                ]);
        }

        try {
            $this->webhookRepository->delete($webhook->id);
            return redirect()->route('webhooks.index', $page)->with('messageSuccess', [
                'status' => 'Delete success',
                'message' => 'This webhook successfully deleted',
            ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => 'Delete failed. Something went wrong',
            ]);
        }
    }

    public function changeStatus(Request $request)
    {
        $webhook = $this->webhookRepository->find($request->id);
        $this->authorize('changeStatus', $webhook);

        if ($request->status == WebhookStatus::ENABLED()->key) {
            $status = WebhookStatus::ENABLED;
        } else {
            $status = WebhookStatus::DISABLED;
        }

        $result = $this->webhookRepository->update($request->id, ['status' => $status]);

        if ($result) {
            return 'This webhook was updated successfully';
        }

        return response()->json([
            'status' => 'Updated failed',
            'message' => 'Updated failed. Something went wrong',
        ], 400);
    }
}
