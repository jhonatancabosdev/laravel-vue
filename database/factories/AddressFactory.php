<?php

use Faker\Generator as Faker;
use App\Address;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'address_1' => $faker->address,
        'address_2' => $faker->address,
        'zipcode' => $faker->postcode,
        'status' => true,
    ];
});
