<?php

namespace App\Jobs;

use App\Models\MessageHistory;
use Illuminate\Bus\Queueable;
use App\Enums\MessageHistoryStatus;
use SunAsterisk\Chatwork\Chatwork;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SunAsterisk\Chatwork\Exceptions\APIException;

class SendMessageToChatwork implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chatwork;
    protected $roomId;
    protected $messages;
    protected $payloadHistoryId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Chatwork $chatwork, $roomId, $messages, $payloadHistoryId)
    {
        $this->chatwork = $chatwork;
        $this->roomId = $roomId;
        $this->messages = $messages;
        $this->payloadHistoryId = $payloadHistoryId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->messages as $message) {
            try {
                // send message to chatwork
                $this->chatwork->room($this->roomId)->messages()->create($message);
                // save success message_history
                $this->saveMessageHistory($this->payloadHistoryId, $message, MessageHistoryStatus::SUCCESS);
            } catch (APIException $error) {
                $this->saveMessageHistory(
                    $this->payloadHistoryId,
                    $message,
                    MessageHistoryStatus::FAILED,
                    $error->getResponse()
                );
            }
        }
    }

    public function saveMessageHistory($payloadHistoryId, $message, $status, $log = '')
    {
        MessageHistory::create([
            'payload_history_id' => $payloadHistoryId,
            'message_content' => $message,
            'status' => $status,
            'log' => $log,
        ]);
    }
}
