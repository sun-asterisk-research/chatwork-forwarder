<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Webhook;
use Faker\Generator as Faker;
use App\Models\PayloadHistory;

$factory->define(PayloadHistory::class, function (Faker $faker, $params) {
    $webhook_id = (isset($params['webhook_id'])) ? $params['webhook_id'] : factory(Webhook::class)->create()->id;
    return [
        'webhook_id' => $webhook_id,
        'params' => $faker->paragraph,
        'status' => 1,
        'log' => $faker->name,
    ];
});
