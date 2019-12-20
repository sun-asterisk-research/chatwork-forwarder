<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Bot;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Bot::class, function (Faker $faker, $params) {
    $user_id = (isset($params['user_id'])) ? $params['user_id'] : factory(User::class)->create()->id;
    return [
        'name' => $faker->name,
        'bot_key' => $faker->md5,
        'user_id' => $user_id
    ];
});
