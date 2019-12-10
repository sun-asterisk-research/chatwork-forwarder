<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\MessageHistory;
use App\Models\PayloadHistory;

$factory->define(MessageHistory::class, function (Faker $faker) {
    return [
        'payload_history_id' => factory(PayloadHistory::class)->create()->id,
        'message_content' => $faker->paragraph,
        'status' => 1,
        'log' => $faker->name,
    ];
});
