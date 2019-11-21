<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use App\Models\Bot;
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

$factory->define(Webhook::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id,
        'bot_id' => factory(Bot::class)->create()->id,
        'name' => $faker->name,
        'token' => Str::random(10),
        'status' => 1,
        'description' => $faker->paragraph,
        'room_id' => $faker->ean8,
        'room_name' => $faker->name,
    ];
});
