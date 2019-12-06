<?php

namespace App\Services;

use App\Models\Webhook;
use SunAsterisk\Chatwork\Chatwork;
use App\Enums\PayloadHistoryStatus;
use App\Enums\MessageHistoryStatus;
use App\Jobs\SendMessageToChatwork;
use SunAsterisk\Chatwork\Exceptions\APIException;

class ForwardChatworkService
{
    protected $webhook;
    protected $params;
    protected $payloadHistory;
    protected $payloadHistoryRepository;
    protected $messageHistoryRepository;

    public function __construct(Webhook $webhook, $params, $payloadHistoryRepository, $messageHistoryRepository)
    {
        $this->webhook = $webhook;
        $this->params = $params;
        $this->payloadHistoryRepository = $payloadHistoryRepository;
        $this->messageHistoryRepository = $messageHistoryRepository;
    }

    /**
     * forward message to chatwork
     *
     * @return void
     */
    public function call()
    {
        $payloads = $this->webhook->payloads;
        $params = $this->params;
        $filledPayloads = [];
        foreach ($payloads as $payload) {
            $conditions = $payload->conditions;
            $isValid = true;
            foreach ($conditions as $condition) {
                // 'return $payloads->fieldName == "value";'
                try {
                    $expression = 'return ' . $condition->field . ' '
                                            . $condition->operator
                                            . ' "' . $condition->value . '";';
                    if (!eval($expression)) {
                        $isValid = false;
                        break;
                    }
                } catch (\ErrorException $e) {
                    $isValid = false;
                    break;
                }
            }

            if ($isValid) {
                array_push($filledPayloads, $payload);
            }
        }

        if ($filledPayloads) {
            // mapping data from params request into payload content
            $messages = [];
            foreach ($filledPayloads as $payload) {
                $message = $this->generateMessage($payload->content, $this->params);
                if ($message) {
                    array_push($messages, $message);
                }
            }

            if ($messages) {
                $this->payloadHistory = $this->savePayloadHistory(PayloadHistoryStatus::SUCCESS);
                // send messages to chatwork
                $this->sendMessages($messages);
            }
        } else {
            $log = 'Not found payload.';
            $this->savePayloadHistory(PayloadHistoryStatus::FAILED, $log);
        }
    }

    /**
     * Create PayloadHistory
     * @param string $status
     * @param string $log
     *
     * @return mixed
     */
    public function savePayloadHistory($status, $log = '')
    {
        $data = [
            'webhook_id' => $this->webhook->id,
            'params' => json_encode($this->params),
            'status' => $status,
            'log' => $log,
        ];

        return $this->payloadHistoryRepository->create($data);
    }

    /**
     * Generate message to payload's content with params to client
     * @param string $content
     * @param string $params
     *
     * @return string
     */
    public function generateMessage($content, $params)
    {
        $isMatching = true;
        $message = preg_replace_callback(
            '#{{(.*?)}}#',
            function ($match) use ($params, &$isMatching) {
                try {
                    $value = 'return '. $match[1] . ';';
                    return eval($value);
                } catch (\ErrorException $e) {
                    // create a failed payload_history when values in payload's content not matching with params payload
                    $isMatching = false;
                    $log = 'Not found ' . $match[1];
                    $this->savePayloadHistory(PayloadHistoryStatus::FAILED, $log);
                }
            },
            $content
        );

        if ($isMatching) {
            return $message;
        }

        return '';
    }

    public function sendMessages($messages)
    {
        $bot = $this->webhook->bot;
        $roomId = $this->webhook->room_id;
        $chatwork = Chatwork::withAPIToken($bot->bot_key);

        foreach ($messages as $key => $message) {
            try {
                $chatwork->room($roomId)->messages()->create($message);
                array_splice($messages, $key, 1);
                // save success message_history
                $this->saveMessageHistory($message, MessageHistoryStatus::SUCCESS);
            } catch (APIException $error) {
                switch ($error->getStatus()) {
                    case 403:
                        // permission denined
                        $this->saveMessageHistory($message, MessageHistoryStatus::FAILED, 'Permission');
                        break 2;
                    case 429:
                        // limit request
                        // add to queue and excute this job after 5 minutes
                        SendMessageToChatwork::dispatch($bot, $roomId, $messages, $this->payloadHistory->id)
                            ->onQueue('high')
                            ->delay(now()
                            ->addMinutes(5));
                        break 2;
                    case 401:
                        // authorized
                        $this->saveMessageHistory($message, MessageHistoryStatus::FAILED, 'Unauthorized');
                        break 2;
                    default:
                        // handle timeout error
                }
            }
        }
    }

    public function saveMessageHistory($message, $status, $log = '')
    {
        $this->messageHistoryRepository->create([
            'payload_history_id' => $this->payloadHistory->id,
            'message_content' => $message,
            'status' => $status,
            'log' => $log,
        ]);
    }
}
