<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Bot;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Bot::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'bot_key' => $faker->md5,
        'user_id' => factory(User::class)->create()->id
    ];
});
