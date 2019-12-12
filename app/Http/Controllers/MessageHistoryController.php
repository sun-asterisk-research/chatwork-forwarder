<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\MessageHistory;
use App\Repositories\Interfaces\MessageHistoryRepositoryInterface as MessageHistoryRepository;

class MessageHistoryController extends Controller
{
    private $messageHistoryRepository;

    public function __construct(MessageHistoryRepository $messageHistoryRepository)
    {
        $this->messageHistoryRepository = $messageHistoryRepository;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App/models/MessageHistory $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(MessageHistory $message)
    {
        $this->authorize('delete', $message);
        try {
            $payloadHistoryId = $message->payloadHistory->id;
            $this->messageHistoryRepository->delete($message->id);

            return redirect(route('history.show', ['history' => $payloadHistoryId]))
                    ->with('messageSuccess', 'This message history successfully deleted');
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', 'Delete failed. Something went wrong');
        }
    }
}
