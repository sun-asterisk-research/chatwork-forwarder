<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Requests\MigrateRequest;
use App\Models\Condition;
use App\Models\Mapping;
use App\Models\Payload;
use App\Models\Webhook;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class MigrateData extends Controller
{
    public function __invoke(MigrateRequest $request, Webhook $webhook)
    {
        $this->authorize('update', $webhook);

        try {
            DB::beginTransaction();
            $client = new Client();
            $response = $client->post($request->input('url') . '/get-config', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $payloads = $data['data']['payloads'];
            $mappings = $data['data']['mappings'];
            $conditions = [];
            $newMappings = [];

            foreach ($payloads as $payload) {
                $newPayload = $webhook->payloads()->create([
                    'content_type' => Payload::TYPE_TEXT,
                    'content' => $payload['content'],
                    'params' => $payload['params'],
                ]);
                foreach ($payload['conditions'] as $condition) {
                    $conditions[] = [
                        'object_id' => $newPayload->id,
                        'field' => $condition['field'],
                        'operator' => $condition['operator'],
                        'value' => $condition['value'],
                        'object_type' => 'App\Models\Payload',
                        'created_at' => now(),
                    ];
                }
            }
            Condition::insert($conditions);

            foreach ($mappings as $mapping) {
                $newMappings[] = [
                    'webhook_id' => $webhook->id,
                    'name' => $mapping['name'],
                    'key' => $mapping['key'],
                    'value' => $mapping['value'],
                    'created_at' => now(),
                ];
            }
            Mapping::insert($newMappings);
            DB::commit();

            return redirect()->route('webhooks.edit', $webhook)
                ->with('messageSuccess', [
                    'status' => 'Migrate success',
                    'message' => 'Migrate config from Chatwork Forwarder success',
                ]);
        } catch (Exception $exception) {
            DB::rollBack();
            logger($exception);
            return redirect()->back()->with('messageFail', [
                'status' => 'Migrate failed',
                'message' => 'Migrate failed. Something went wrong',
            ]);
        }
    }
}
