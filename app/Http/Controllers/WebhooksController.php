<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Webhook;
use App\Repositories\Eloquents\WebhookRepository as WebhookRepository;

class WebhooksController extends Controller
{
    private $webhookRepository;

    public function __construct(WebhookRepository $webhookRepository)
    {
        $this->webhookRepository = $webhookRepository;
    }

    public function index()
    {
        $webhooks = $this->webhookRepository->getAllByUser();
     
        return view('webhooks.index', compact('webhooks'));
    }
}
