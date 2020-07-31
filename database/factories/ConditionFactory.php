<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Condition;
use App\Models\Payload;
use Faker\Generator as Faker;

$factory->define(Condition::class, function (Faker $faker) {
    $operator = ['==', '!=', '>', '>=', '<', '<='];
    return [
        'field' => '$params->' . $faker->word,
        'operator' => $operator[array_rand($operator)],
        'value' => $faker->name,
        'object_id' => factory(Payload::class)->create()->id,
        'object_type' => 'App\Models\Payload',
    ];
});
