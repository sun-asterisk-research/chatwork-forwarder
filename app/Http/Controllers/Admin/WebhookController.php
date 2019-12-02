<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\WebhookRepositoryInterface as WebhookRepository;

class WebhookController extends Controller
{
    private $webhookRepository;

    public function __construct(WebhookRepository $webhookRepository)
    {
        $this->webhookRepository = $webhookRepository;
    }

    public function index()
    {
        $webhooks = $this->webhookRepository->getAll();

        return view('admins.webhooks.index', compact('webhooks'));
    }
}
