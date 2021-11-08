<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\MessageHistory;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use JoliCode\Slack\Api\Client;
use App\Enums\MessageHistoryStatus;
use App\Enums\SlackStatus;
use JoliCode\Slack\Exception\SlackErrorResponse;

class SendMessageToSlack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $slack;
    protected $roomId;
    protected $messages;
    protected $payloadHistoryId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Client $slack, $roomId, $messages, $payloadHistoryId)
    {
        $this->slack = $slack;
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
                $this->slack->chatPostMessage([
                    'channel' => $this->roomId,
                    'blocks' => $message,
                ]);
                // save success message_history
                $this->saveMessageHistory($this->payloadHistoryId, $message, MessageHistoryStatus::SUCCESS);
            } catch (SlackErrorResponse $error) {
                if ($error->getErrorCode() == SlackStatus::INVALID_BLOCKS) {
                    $this->slack->chatPostMessage([
                        'channel' => $this->roomId,
                        'text' => $message,
                    ]);

                    $this->saveMessageHistory($this->payloadHistoryId, $message, MessageHistoryStatus::SUCCESS);
                } else {
                    $this->saveMessageHistory(
                        $this->payloadHistoryId,
                        $message,
                        MessageHistoryStatus::FAILED,
                        $error->getResponseMetadata()
                    );
                }
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
