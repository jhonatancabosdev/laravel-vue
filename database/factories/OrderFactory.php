<?php

use App\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'payment_method' => 'paypal',
        'tax_rate' => 0,
        'tax' => 0,
        'shipping_cost' => 2,
        'subtotal' => 20,
        'total' => 22,
        'order_status_id' => 1,
        'shipping_id' => 1,
        'customer_id' => 3,
        'confirmed' => 1,
    ];
});
