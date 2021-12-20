<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Webhook;

class MigrateDataForm extends Controller
{
    public function __invoke(Webhook $webhook)
    {
        return view('webhooks.migrate')->with([
            'webhook' => $webhook,
        ]);
    }
}
