<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\Condition;
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

    public function forwardMessage(Request $request, $token)
    {
        $params = json_decode(json_encode((object)$request->all()), false);
        $webhook = Webhook::enable()->where('token', $token)->first();

        if ($webhook) {
            $forwardChatworkService = new ForwardChatworkService(
                $webhook,
                $params,
                $this->payloadHistoryRepository,
                $this->messageHistoryRepository
            );

            $forwardChatworkService->call();

            return response()->json('Excuted successfully');
        }

        return response()->json('Webhook not found. Please try again');
    }
}
