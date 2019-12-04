<?php

namespace App\Services;

use App\Models\Webhook;
use App\Enums\PayloadHistoryStatus;

class ForwardChatworkService
{
    protected $webhook;
    protected $params;
    protected $payloadHistoryRepository;

    public function __construct(Webhook $webhook, $params, $payloadHistoryRepository)
    {
        $this->webhook = $webhook;
        $this->params = $params;
        $this->payloadHistoryRepository = $payloadHistoryRepository;
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
                $expression = 'return ' . $condition->field . ' '
                                        . $condition->operator . ' "'
                                        . $condition->value . '";';
                if (!eval($expression)) {
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
                    $this->savePayloadHistory(PayloadHistoryStatus::SUCCESS);
                    array_push($messages, $message);
                }
            }

            // send messages to chatwork
            dd($messages);
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

        $this->payloadHistoryRepository->create($data);
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
                } catch (\ErrorException $rr) {
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
}
