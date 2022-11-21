<?php

use App\Shipping;
use Faker\Generator as Faker;

$factory->define(Shipping::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'base_cost' => $faker->numberBetween(10, 20)
    ];
});
