<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Webhook;
use Faker\Generator as Faker;
use App\Models\PayloadHistory;

$factory->define(PayloadHistory::class, function (Faker $faker) {
    return [
        'webhook_id' => factory(Webhook::class)->create()->id,
        'params' => $faker->paragraph,
        'status' => 1,
        'log' => $faker->name,
    ];
});
