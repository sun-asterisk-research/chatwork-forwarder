<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Enums\WebhookStatus;
use App\Models\Bot;
use App\Models\User;
use App\Repositories\Interfaces\WebhookRepositoryInterface as WebhookRepository;

class WebhookController extends Controller
{
    private $webhookRepository;

    public function __construct(WebhookRepository $webhookRepository)
    {
        $this->webhookRepository = $webhookRepository;
    }

    public function index(Request $request)
    {
        $searchParams = $request->get('search');
        $perPage = config('paginate.perPage');
        $webhooks = $this->webhookRepository->getAllAndSearch($perPage, $searchParams);
        $users = User::all()->pluck('id', 'name');
        
        return view('admins.webhooks.index', compact('webhooks', 'users'));
    }

    public function show(Webhook $webhook)
    {
        $payloads = $webhook->payloads()->get();
        $bot = Bot::findOrFail($webhook->bot_id);

        return view('admins.webhooks.detail', compact('webhook', 'payloads', 'bot'));
    }
}
