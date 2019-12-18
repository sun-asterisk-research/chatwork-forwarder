<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Enums\PayloadHistoryStatus;
use App\Repositories\Interfaces\WebhookRepositoryInterface as WebhookRepository;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface as MessageHistoryRepository;
use App\Repositories\Interfaces\PayloadHistoryRepositoryInterface as PayloadHistoryRepository;

class PayloadHistoryController extends Controller
{
    private $payloadHistoryRepository;
    private $webhookRepository;
    private $messageHistoryRepository;

    public function __construct(
        PayloadHistoryRepository $payloadHistoryRepository,
        MessageHistoryRepository $messageHistoryRepository,
        WebhookRepository $webhookRepository
    ) {
        $this->payloadHistoryRepository = $payloadHistoryRepository;
        $this->webhookRepository = $webhookRepository;
        $this->messageHistoryRepository = $messageHistoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchParams = $request->search;
        $perPage = config('paginate.perPage');
        $payloadHistories = $this->payloadHistoryRepository->getAllAndSearch($perPage, $searchParams);
        $webhooks = $this->webhookRepository->getAll()->pluck('id', 'name');
        $payloadHistoryStatuses = PayloadHistoryStatus::toArray();

        return view(
            'admins.payload_histories.index',
            compact('payloadHistories', 'webhooks', 'payloadHistoryStatuses')
        );
    }

    public function show(Request $request, $id)
    {
        $keyword = $request->get('search');
        $payloadHistory = $this->payloadHistoryRepository->find($id);

        $this->authorize('show', $payloadHistory);

        $messageHistories = $this->messageHistoryRepository->GetAllAndSearch($id, $keyword);

        return view('admins.payload_histories.show', compact('payloadHistory', 'messageHistories'));
    }
}
