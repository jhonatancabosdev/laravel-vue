<?php

namespace FME\EloquenceCsvFeed\Handlers;

use App\Shop\Products\Product;
use App\Shop\Couriers\Courier;
use FME\EloquenceCsvFeed\Helper;

class ProductsNoImagesHandler extends \FME\EloquenceCsvFeed\Base\EloquenceCsvFeedHandler
{
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Set feed to feed attribute
     *
     * @return [type] [description]
     */
    public function handle() : void
    {
        // handle request recieved
        //
        $fh = fopen('php://output', 'w');

        $data = [];

        Product::whereStatus('status', true)->chunk(300, function ($products) use (&$data) {
            foreach ($products as $product) {
                if ($product->cover == env('DEFAULT_NO_IMAGE') || $product->images->isEmpty()) {
                    $data[] = $this->transform($product);
                }
            }
        });

        $this->setFeed($data);
    }

    /**
     * transform product to array
     *
     * @param product
     * @return array
     */
    public function transform($item) : array
    {
        return $item->toArray();
    }
}
