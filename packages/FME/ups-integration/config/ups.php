<?php

return [
    // enable request caching
    'cache' => true,
    'sandbox' => true,
    'accessKey' => 'ED82603D09D22E75',
    'userId' => 'BrutWorms18',
    'password' => 'Worms123',
    'accountNumber' => '5X7526',
    'services' => [
        [
            'label' => 'UPS 2nd Day Air',
            'Code' => '02',
            'Description' => 'UPS 2nd Day Air',
            'shipstation_carrier_code' => 'ups_walleted',
            'shipstation_service_code' => 'ups_2nd_day_air',
        ],
        [
            'label' => 'UPS Ground',
            'Code' => '03',
            'Description' => 'UPS Ground',
            'shipstation_carrier_code' => 'ups_walleted',
            'shipstation_service_code' => 'ups_2nd_day_air',
        ],
    ],
    'shipFrom' =>  [
        "name" => "Brut Worm Farms",
        "company" => "Brut Worm Farms, Inc.",
        'email' => 'karen.larson@brutwormfarms.com',
        "street1" => "450 Industrial Park Road",
        "street2" => "Karen Larson",
        "street3" => null,
        "city" => "BROOTEN",
        "state" => "MN",
        "postalCode" => "56316",
        "country" => "US",
        "phone" => config('default-variables.phone'),
        "residential" => false
    ]
];
