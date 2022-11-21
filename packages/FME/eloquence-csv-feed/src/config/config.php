<?php

return [

    /**
     * -----------------------------------------------------------
     * FME database feed package
     * -----------------------------------------------------------
     *
     * All atributes are also the headers on CSV file
     */
    'models' => [

        // models name also used in routes
        // for exemple products model is requested in
        // @link https://resourcefulsupply.test/api/v1/fme/products/
        'products' => [

            // namespace is the model namespace
            // used to get model instance
            'namespace' => \App\Product::class,
            'handler' => \FME\EloquenceCsvFeed\Handlers\ProductHandler::class,
        ],
    ]

];
