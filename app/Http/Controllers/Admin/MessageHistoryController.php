<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\MessageHistory;
use App\Http\Controllers\Controller;
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

            return redirect(route('admin.history.show', ['history' => $payloadHistoryId]))
                ->with('messageSuccess', [
                    'status' => 'Delete success',
                    'message' => 'This message history successfully deleted',
                ]);
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', [
                'status' => 'Delete failed',
                'message' => 'Delete failed. Something went wrong',
            ]);
        }
    }
}
