<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;
use App\Services\ForwardChatworkService;
use App\Repositories\Interfaces\PayloadHistoryRepositoryInterface as PayloadHistoryRepository;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface as MessageHistoryRepository;

class ForwardChatworkController extends Controller
{
    protected $payloadHistoryRepository;
    protected $messageHistoryRepository;

    public function __construct(PayloadHistoryRepository $payHisRepo, MessageHistoryRepository $mesHisRepo)
    {
        $this->payloadHistoryRepository = $payHisRepo;
        $this->messageHistoryRepository = $mesHisRepo;
    }

    /**
     * api/v1/webhooks/{token}
     *
     * This api to call a webhook with webhook_token.
     * The params to send to this api is a hash and it not have a fixed structure.
     * Following example below to understand more.
     *
     * Example:
     * {
     *    status: 'active',
     *    user: {
     *      name: 'asd',
     *      age: 20
     *    }
     * }
     *
     * @response {
     *  "message" : "Excuted successfully"
     * }
     * @response 404 {
     *  "message" : "Webhook not found. Please try again"
     * }
     */
    public function forwardMessage(Request $request, $token)
    {
        $params = json_decode(json_encode($request->all()), true);
        $webhook = Webhook::enable()->where('token', $token)->first();

        if ($webhook) {
            $forwardChatworkService = new ForwardChatworkService(
                $webhook,
                $params,
                $this->payloadHistoryRepository,
                $this->messageHistoryRepository
            );

            $forwardChatworkService->call();

            return response()->json('Excuted successfully', 200);
        }

        return response()->json('Webhook not found. Please try again', 404);
    }
}
