<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Template;
use App\Models\User;
use App\Enums\TemplateStatus;
use Faker\Generator as Faker;

$factory->define(Template::class, function (Faker $faker, $params) {
    $user_id = (isset($params['user_id'])) ? $params['user_id'] : factory(User::class)->create()->id;
    return [
        'user_id' => $user_id,
        'name' => $faker->name,
        'content' => $faker->paragraph,
        'params' => $faker->paragraph,
        'status' => TemplateStatus::PUBLIC,
    ];
});
