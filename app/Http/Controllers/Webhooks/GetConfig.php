<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Webhook;

class GetConfig extends Controller
{
    public function __invoke($token)
    {
        $webhook = Webhook::where('token', $token)
            ->with(['payloads.conditions', 'mappings'])
            ->firstOrFail();

        return response()->json([
            'data' => [
                'payloads' => $webhook->payloads,
                'mappings' => $webhook->mappings,
            ],
        ]);
    }
}
