<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Payload;
use App\Models\Webhook;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Payload::class, function (Faker $faker, $params) {
    $webhook_id = (isset($params['webhook_id'])) ? $params['webhook_id'] : factory(Webhook::class)->create()->id;
    return [
        'webhook_id' => $webhook_id,
        'content' => $faker->paragraph,
    ];
});
