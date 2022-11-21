<?php

use App\City;
use Faker\Generator as Faker;

$factory->define(City::class, function (Faker $faker) {
    return [
        'name' => $faker->city,
        'timezone' => $faker->timezone,
        'status' => true,
        'state_id' => function() {
            return factory('App\State')->create()->id;
        },
    ];
});
