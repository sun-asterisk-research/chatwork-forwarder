<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Mapping;
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

$factory->define(Mapping::class, function (Faker $faker) {
    return [
        'webhook_id' => factory(Webhook::class)->create()->id,
        'name' => $faker->name,
        'key' => Str::random(10),
        'value' => $faker->name,
    ];
});
