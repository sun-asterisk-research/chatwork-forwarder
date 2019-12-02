<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\Condition;
use Illuminate\Http\Request;
use App\Services\ForwardChatworkService;
use App\Repositories\Interfaces\PayloadHistoryRepositoryInterface as PayloadHistoryRepository;

class ForwardChatworkController extends Controller
{
    protected $payloadHistoryRepository;

    public function __construct(PayloadHistoryRepository $payloadHistoryRepository)
    {
        $this->payloadHistoryRepository = $payloadHistoryRepository;
    }

    public function forwardMessage(Request $request, $token)
    {
        $params = json_decode(json_encode((object) $request->all()), false);
        $webhook = Webhook::where('token', $token)->first();

        if ($webhook) {
            $forwardChatworkService = new ForwardChatworkService($webhook, $params, $this->payloadHistoryRepository);
            $forwardChatworkService->call();
        } else {
            return null;
        }
    }
}
