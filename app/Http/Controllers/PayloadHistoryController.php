<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\PayloadHistoryRepositoryInterface as PayloadHistoryRepository;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface as MessageHistoryRepository;

class PayloadHistoryController extends Controller
{
    private $payloadHistoryRepository;
    private $messageHistoryRepository;

    public function __construct(
        PayloadHistoryRepository $payloadHistoryRepository,
        MessageHistoryRepository $messageHistoryRepository
    ) {
        $this->payloadHistoryRepository = $payloadHistoryRepository;
        $this->messageHistoryRepository = $messageHistoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
