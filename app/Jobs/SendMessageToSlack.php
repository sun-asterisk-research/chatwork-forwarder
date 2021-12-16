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
use App\Models\Bot;
use JoliCode\Slack\ClientFactory;
use JoliCode\Slack\Exception\SlackErrorResponse;

class SendMessageToSlack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $bot;
    protected $roomId;
    protected $data;
    protected $payloadHistoryId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bot $bot, $roomId, $data, $payloadHistoryId)
    {
        $this->bot = $bot;
        $this->roomId = $roomId;
        $this->data = $data;
        $this->payloadHistoryId = $payloadHistoryId;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $slack = ClientFactory::create($this->bot->bot_key);
        try {
            // send message to chatwork
            $slack->chatPostMessage([
                'channel' => $this->roomId,
                $this->data['content_type'] => $this->data['message'],
            ]);
            // save success message_history
            $this->saveMessageHistory($this->payloadHistoryId, $this->data['message'], MessageHistoryStatus::SUCCESS);
        } catch (SlackErrorResponse $error) {
            $this->saveMessageHistory(
                $this->payloadHistoryId,
                $this->data['message'],
                MessageHistoryStatus::FAILED,
                $error->getMessage()
            );
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
