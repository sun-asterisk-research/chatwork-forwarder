<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayloadHistory;
use App\Enums\PayloadHistoryStatus;
use SebastianBergmann\CodeCoverage\Exception;
use App\Repositories\Interfaces\WebhookRepositoryInterface as WebhookRepository;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface as MessageHistoryRepository;
use App\Repositories\Interfaces\PayloadHistoryRepositoryInterface as PayloadHistoryRepository;

class PayloadHistoryController extends Controller
{
    private $payloadHistoryRepository;
    private $messageHistoryRepository;
    private $webhookRepository;

    public function __construct(
        PayloadHistoryRepository $payloadHistoryRepository,
        MessageHistoryRepository $messageHistoryRepository,
        WebhookRepository $webhookRepository
    ) {
        $this->payloadHistoryRepository = $payloadHistoryRepository;
        $this->messageHistoryRepository = $messageHistoryRepository;
        $this->webhookRepository = $webhookRepository;
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
        $webhooks = $this->webhookRepository->getAllByUserForDropdown()->pluck('id', 'name');
        $payloadHistoryStatuses = PayloadHistoryStatus::toArray();

        return view('payload_histories.index', compact('payloadHistories', 'webhooks', 'payloadHistoryStatuses'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $keyword = $request->get('search');
        $payloadHistory = $this->payloadHistoryRepository->find($id);

        $this->authorize('show', $payloadHistory);

        $messageHistories = $this->messageHistoryRepository->getAllAndSearch($id, $keyword);

        return view('history.show', compact('payloadHistory', 'messageHistories'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App/models/PayloadHistory $history
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayloadHistory $history)
    {
        $this->authorize('delete', $history);
        try {
            $this->payloadHistoryRepository->delete($history->id);

            return redirect(route('history.index'))
                ->with('messageSuccess', [
                    'status' => 'Delete success',
                    'message' => 'This payload history successfully deleted',
                ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => 'Delete failed. Something went wrong',
            ]);
        }
    }
}
