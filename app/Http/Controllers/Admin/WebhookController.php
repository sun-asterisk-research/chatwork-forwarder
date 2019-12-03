<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $keyword = $request->get('search');
        $perPage = config('paginate.perPage');
        $webhooks = $this->webhookRepository->getAllAndSearch($perPage, $keyword);

        return view('admins.webhooks.index', compact('webhooks'));
    }
}
