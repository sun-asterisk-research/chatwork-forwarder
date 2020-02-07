<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\PayloadHistory;
use App\Enums\PayloadHistoryStatus;
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
        $payloadHistories = $this->payloadHistoryRepository->getAllByUserAndSearch($perPage, $searchParams);
        if ($payloadHistories->count() == 0 && $payloadHistories->previousPageUrl()) {
            return redirect($payloadHistories->previousPageUrl());
        } else {
            $webhooks = $this->webhookRepository->getAllByUserForDropdown()->pluck('id', 'name');
            $payloadHistoryStatuses = PayloadHistoryStatus::toArray();

            return view(
                'payload_histories.index',
                compact('payloadHistories', 'webhooks', 'payloadHistoryStatuses')
            );
        }
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

        return view('payload_histories.show', compact('payloadHistory', 'messageHistories'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App/models/PayloadHistory $history
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, PayloadHistory $history)
    {
        $this->authorize('delete', $history);
        $page = $request->page ? ['page' => $request->page] : null;
        try {
            $this->payloadHistoryRepository->delete($history->id);

            return redirect(route('history.index', $page))
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

    public function recheck(Request $request)
    {
        $errorFields = [];
        $result = [];
        $payloadHistory = PayloadHistory::findOrFail($request->id);
        $this->authorize('recheck', $payloadHistory);
        $params = json_decode($payloadHistory->params, true);
        $payloads = $payloadHistory->webhook->payloads;
        foreach ($payloads as $payload) {
            $content = $payload->content;
            $value = $this->getStringsBetweebBrackets($content);
            $result = array_merge($result, $value);
        }
        foreach ($result as $value) {
            try {
                $this->getValues($params, $value);
            } catch (\Throwable $th) {
                array_push($errorFields, trim($value));
                continue;
            }
        }
        if (count($errorFields) != 0) {
            return response()->json([
                'error' => true,
                'data' => array_unique($errorFields),
            ]);
        } else {
            return response()->json([
                'error' => false,
            ]);
        }
    }

    private function getStringsBetweebBrackets($str)
    {
        $regex1 = '#{{(.*?)}}#';
        preg_match_all($regex1, $str, $matches1);

        $regex2 = '#{!!(.*?)!!}#';
        preg_match_all($regex2, $str, $matches2);

        return array_merge($matches1[1], $matches2[1]);
    }

    public function getValues($data, $field)
    {
        $attributes = explode('.', trim($field));
        if ($attributes[0] === '$params') {
            array_shift($attributes);
        }
        $currentValue = $data;
        foreach ($attributes as $attr) {
            $currentValue = $currentValue[$attr];
        }

        return $currentValue;
    }
}
