<?php

namespace FME\EloquenceCsvFeed\Handlers;

use App\Shop\Products\Product;
use App\Shop\Categories\Category;
use App\Shop\Categories\Repositories\CategoryRepository;

class CategoryHandler extends \FME\EloquenceCsvFeed\Base\EloquenceCsvFeedHandler
{
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function handle() : void
    {
        // // handle request recieved
        // //
        $fh = fopen('php://output', 'w');

        $data = [];

        Category::chunk(300, function ($categories) use (&$data) {
            foreach ($categories as $category) {
                $parentCategories = Category::remember(config('global-variables.cache_life_time'))
                                    ->ancestorsAndSelf(
                                        $category,
                                        ['id', 'slug', 'name', 'cover']
                                    );
                //
                $breadcrumb = $parentCategories->pluck('name')->toArray();

                $category = implode(' > ', $breadcrumb);

                if ($category == 'Default' || trim($category) == '') {
                    continue;
                }

                if (!in_array($category, array_column($data, 'category'))) {
                    $data[] = ['category' => $category];
                }
            }
        });

        $this->setFeed($data);
    }

    public function transform($item) : array
    {
        return [];
    }

    public function oldHandle()
    {
        Product::where('status', true)->chunk(300, function ($products) use (&$data) {
            foreach ($products as $product) {
                $parentCategories = Category::remember(config('global-variables.cache_life_time'))
                                    ->ancestorsAndSelf(
                                        $product->category,
                                        ['id', 'slug', 'name', 'cover']
                                    );
                //
                $breadcrumb = $parentCategories->pluck('name')->toArray();

                $category = implode(' > ', $breadcrumb);

                if ($category == 'Default' || trim($category) == '') {
                    continue;
                }

                if (!in_array($category, array_column($data, 'category'))) {
                    $data[] = ['category' => $category];
                }
            }
        });

        $this->setFeed($data);
    }
}
