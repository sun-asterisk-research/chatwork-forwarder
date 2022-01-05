<?php

namespace App\Services;

use Throwable;
use ErrorException;
use App\Models\Webhook;
use JoliCode\Slack\ClientFactory;
use JoliCode\Slack\Api\Client;
use App\Enums\PayloadHistoryStatus;
use App\Enums\MessageHistoryStatus;
use App\Jobs\SendMessageToSlack;
use SunAsterisk\Chatwork\Exceptions\APIException;
use JoliCode\Slack\Exception\SlackErrorResponse;
use App\Support\Support;
use App\Enums\SlackStatus;

class ForwardSlackService
{
    use Support;

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
                try {
                    $paramValue = $this->getValues($params, $condition->field);
                    if (is_bool($paramValue)) {
                        $paramValue = $paramValue ? 'true' : 'false';
                    }
                    if (is_null($paramValue)) {
                        $paramValue = 'null';
                    }

                    if (!$this->checkCondition($paramValue, $condition)) {
                        $isValid = false;
                        break;
                    }
                } catch (Throwable | ErrorException $e) {
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
            $output = [];
            foreach ($filledPayloads as $payload) {
                $data = [
                    'message' => $this->generateMessage($payload->content, $this->params),
                    'content_type' => $payload->content_type,
                ];
                if ($data['message']) {
                    array_push($output, $data);
                }
            }

            if (count($output) > 0) {
                $payloadHistory = $this->savePayloadHistory(PayloadHistoryStatus::SUCCESS);
                // send messages to chatwork
                $slack = ClientFactory::create($this->webhook->bot->bot_key);
                $this->sendMessages($output, $slack, $payloadHistory->id);
            }
        } else {
            $log = 'This payload does not match any conditions in this webhook.';
            $this->savePayloadHistory(PayloadHistoryStatus::FAILED, $log);
        }
    }

    public function checkCondition($value, $condition)
    {
        switch ($condition->operator) {
            case '==':
                return $value == $condition->value;
                break;
            case '!=':
                return $value != $condition->value;
                break;
            case '>':
                return $value > $condition->value;
                break;
            case '>=':
                return $value >= $condition->value;
                break;
            case '<':
                return $value < $condition->value;
                break;
            case '<=':
                return $value <= $condition->value;
                break;
            case 'Match':
                return preg_match($condition->value, $value);
                break;
            default:
                return false;
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
            'params' => json_encode($this->params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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
            '#{!!(.*?)!!}#',
            function ($match) use ($params, &$isMatching) {
                try {
                    $requestValue = (string) $this->getValues($params, $match[1]);
                    if (is_array($requestValue)) {
                        $requestValue = json_encode(
                            $requestValue,
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        );
                    }

                    return $requestValue;
                } catch (Throwable | ErrorException $e) {
                    // create a failed payload_history when values in payload's content not matching with params payload
                    $isMatching = false;
                    $log = 'Not found ' . $match[1];
                    $this->savePayloadHistory(PayloadHistoryStatus::FAILED, $log);
                }
            },
            $content
        );

        $mapers = $this->webhook->mappings;
        $message = preg_replace_callback(
            '#{{(.*?)}}#',
            function ($match) use ($params, $mapers, &$isMatching) {
                try {
                    $requestValue = $this->getValues($params, $match[1]);
                    if (is_array($requestValue)) {
                        $requestValue = json_encode(
                            $requestValue,
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        );
                    }
                    $mappingValue = $mapers->where('key', (string) $requestValue)->first();

                    return $mappingValue ? $mappingValue->value : $requestValue;
                } catch (Throwable | ErrorException $e) {
                    // create a failed payload_history when values in payload's content not matching with params payload
                    $isMatching = false;
                    $log = 'Not found ' . $match[1];
                    $this->savePayloadHistory(PayloadHistoryStatus::FAILED, $log);
                }
            },
            $message
        );

        $message = preg_replace_callback(
            '#\[\[(.*?)\]\]#',
            function ($match) use ($params, $mapers, &$isMatching) {
                try {
                    $requestValue = $this->getValues($params, $match[1]);
                    if (is_array($requestValue)) {
                        $requestValue = json_encode(
                            $requestValue,
                            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                        );
                    }

                    $mappings = $mapers->map(function ($item) {
                        $data['key'] = "/([^\ ]+)?$item->key/";
                        $data['value'] = $item->value;

                        return $data;
                    });
                    $mappingValues = preg_replace(
                        $mappings->pluck('key')->toArray(),
                        $mappings->pluck('value')->toArray(),
                        $requestValue
                    );

                    return $mappingValues ? $mappingValues : $requestValue;
                } catch (Throwable | ErrorException $e) {
                    // create a failed payload_history when values in payload's content not matching with params payload
                    $isMatching = false;
                    $log = 'Not found ' . $match[1];
                    $this->savePayloadHistory(PayloadHistoryStatus::FAILED, $log);
                }
            },
            $message
        );

        if ($isMatching) {
            return $message;
        }

        return null;
    }

    public function sendMessages($output, Client $slack, $payloadHistoryId)
    {
        $roomId = $this->webhook->room_id;
        foreach ($output as $data) {
            try {
                $slack->chatPostMessage([
                    'channel' => $roomId,
                    $data['content_type'] => $data['message'],
                    'link_names' => true,
                ]);
                $this->saveMessageHistory($data['message'], MessageHistoryStatus::SUCCESS, $payloadHistoryId);
            } catch (SlackErrorResponse $error) {
                switch ($error->getErrorCode()) {
                    case SlackStatus::NO_PERMISSION:
                        $this->saveMessageHistory(
                            $data['message'],
                            MessageHistoryStatus::FAILED,
                            $payloadHistoryId,
                            'Permission'
                        );
                        break;
                    case SlackStatus::RATE_LIMITED:
                        SendMessageToSlack::dispatch(
                            $this->webhook->bot,
                            $roomId,
                            $data,
                            $payloadHistoryId
                        )
                        ->onQueue('high')
                        ->delay(now()->addSeconds(10));
                        break;
                    case SlackStatus::INVALID_AUTH:
                        $this->saveMessageHistory(
                            $data['message'],
                            MessageHistoryStatus::FAILED,
                            $payloadHistoryId,
                            'Unauthorized'
                        );
                        break;
                    default:
                        $this->saveMessageHistory(
                            $data['message'],
                            MessageHistoryStatus::FAILED,
                            $payloadHistoryId,
                            $error->getMessage()
                        );
                        break;
                }
            }
        }
    }

    public function saveMessageHistory($message, $status, $payloadHistoryId, $log = '')
    {
        $this->messageHistoryRepository->create([
            'payload_history_id' => $payloadHistoryId,
            'message_content' => $message,
            'status' => $status,
            'log' => $log,
        ]);
    }
}
