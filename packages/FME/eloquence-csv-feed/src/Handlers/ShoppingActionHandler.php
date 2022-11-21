<?php

namespace FME\EloquenceCsvFeed\Handlers;

use App\Shop\Couriers\Courier;
use App\Shop\Products\Product;
use App\Shop\Addresses\Address;
use FME\EloquenceCsvFeed\Helper;
use App\Custom\UpsShipping\UpsRate;
use FME\EloquenceCsvFeed\Base\EloquenceCsvFeedHandler;
use App\Custom\UpsShipping\Repositories\UpsShippingRepository;

class ShoppingActionHandler extends EloquenceCsvFeedHandler
{
    /**
     * @param $model model name
     * @param $startAt limit from
     * @param $endAt limit ends
     */
    public function __construct($model = null)
    {
        $this->model = $model;
    }

    /**
     * Get product shipping price
     *
     * @param  Product $product
     * @return float
     */
    public function getShippingRates($product, $address)
    {
        $shippingPrice = 0;

        $rates = UpsRate::where('weight', $product->weight)
                        ->where('address_id', $address->id)
                        ->first();

        if ($rates instanceof UpsRate) {
            return $rates->value;
        }

        try {
            $courier = Courier::remember(config('global-variables.cache_life_time'))->find(3);

            $value = app('UpsHandler')
                ->setAddress($address)
                ->initUpsRepository()
                ->setCourier($courier)
                ->getProductsShippingCost(collect()->push($product));

            $upsRate = UpsRate::create([
                'value' => $value,
                'weight' => $product->weight,
                'weight_uom' => $product->weight_uom,
                'address_id' => $address->id,
            ]);

            return $value;
        } catch (\Exception $e) {
            return 14.95;
        }
    }

    /**
     * Set feed to feed attribute
     *
     * @return [type] [description]
     */
    public function handle() : void
    {
        $data = [];
        $fh = fopen('php://output', 'w');

        $query = Product::where('id', '!=', 1)
                        ->where('availability_id', 3)
                        ->where('shopping_actions_price', '>', 0)
                        ->where('shopping_actions_active', 1);

        if ($this->hasOffset()) {
            $query = $query->offset((int)$this->offset);
        }

        if ($this->hasLimit()) {
            $query = $query->limit((int)$this->limit);
        }

        if ($this->hasOffset() && !$this->hasLimit()) {
            $limit = Product::count();
            $query = $query->limit($limit);
        }

        $query->chunk(150, function ($products) use (&$data) {
            foreach ($products as $product) {
                if ($product->cover == config('global-variables.default_no_image')) {
                    continue;
                }

                $data[] = $this->transform($product);
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
    public function transform($product) : array
    {
        $product->loadMissing('images', 'brand', 'availability', 'category');

        $address = Address::staticAddress();

        $category = $product->category->googleCategory->getCustomLabel(1) ?? 'Hardware';

        $google_product_category = ($product->category_id !== 0) ? $product->category->googleCategory->name : 'Hardware';

        if ($google_product_category == '--') {
            $google_product_category = 'Hardware';
        }

        $title = ucfirst($product->title);
        $price = $product->shopping_actions_price;
        $cost = $product->cost_price;

        if ($product->min_order_quantity > 1) {
            $price *= $product->min_order_quantity;
            $cost *= $product->min_order_quantity;
            $title = '('.$product->min_order_quantity.' Pack) '.ucfirst($product->title);
        }

        $productLink = route('front.get.product', [
            'name' => str_slug($product->seoTitle),
            'id' => $product->id
        ]);

        $productImage = asset("storage/$product->cover");

        $additionalImage = $product->images->isEmpty() ? '' : asset("storage/" . $product->images->first()->src);

        return [

            'id' 			=> 'SA' . $product->id,

            'title' 		=> $title,

            'description' 	=> html_entity_decode($product->long_description1),

            'link' 			=> str_replace('dev.', '', $productLink),

            'image_link'    => str_replace('dev.', '', $productImage),

            'additional_image_link' => str_replace('dev.', '', $additionalImage),

            'availability'   => $product->status ? 'in stock' : 'out of stock',

            'Custom_label_0' => $category,

            'Custom_label_1' => $product->category->googleCategory->getCustomLabel(2),
            
            'Custom_label_2' => $product->category->googleCategory->getCustomLabel(3),

            'Custom_label_3' => $product->availability_message ?? $product->category->googleCategory->getCustomLabel(4),

            'Custom_label_4' => 'shopping_actions',

            'Custom_label_5' => $product->category->googleCategory->getCustomLabel(6),

            'cost_of_goods_sold' => number_format($cost, 2, '.', '') . ' USD',

            'price'    		=> number_format($price, 2, '.', '') . ' USD',

            'sale_price' 	=> number_format($price, 2, '.', '') . ' USD',

            'google_product_category' => $google_product_category,

            'product_type'  => $product->availability->name,

            'brand'			=> html_entity_decode($product->brand->name),

            'mpn'			=> $product->manufacturer_part_number,

            'gtin'			=> $product->upc,

            'condition'		=> 'new',

            'shipping_weight' => $product->weight . ' lb',

            'shipping_length' => $product->length . ' in',

            'shipping_width' => $product->width . ' in',

            'shipping_height' => $product->height . ' in',

            'shipping_label' => 'paid',

            'tax'			=> 'US:PA:6.00:y',

            'max_handling_time' => $product->max_handling_time,

            'min_handling_time' => $product->min_handling_time,

        ];
    }
}
