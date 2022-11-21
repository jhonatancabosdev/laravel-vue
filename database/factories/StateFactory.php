<?php

use App\State;
use Faker\Generator as Faker;

$factory->define(State::class, function (Faker $faker) {
    return [
        'name' => $faker->state,
        'abv' => $faker->word,
        'country_id' => 1,
        'status' => true
    ];
});
