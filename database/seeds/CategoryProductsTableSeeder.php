<?php

use App\{Category, Product};
use Illuminate\Database\Seeder;

class CategoryProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// need to create 10 categories then for each
    	// category we need to make 2 to 5 childs
        factory(Category::class, 10)->create()->each(function($parent){

        	factory(Category::class, mt_rand(1, 4))->create()->each(function($child) use ($parent){

	        	$child->parent()->associate($parent)->save();

	        	factory(Product::class, mt_rand(5, 30))->create()->each(function($product) use ($parent, $child) {

	        		// select random category to append product
        		    $category = [$parent, $child][mt_rand(0, 1)];

	                $category->products()->save($product);
	            });

        	});

        });
    }
}
