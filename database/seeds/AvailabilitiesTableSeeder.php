<?php

use App\{ Availability, Product };
use Illuminate\Database\Seeder;

class AvailabilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Availability::create([
        	'id' => 0,
        	'name' => 'Out of stock',
        	'slug' => 'out-of-stock',
        ]);

        Availability::where('id', 1)->update(['id' => 0]);

        $instock = Availability::create([
        	'id' => 1,
        	'name' => 'In stock',
        	'slug' => 'in-stock',
        ]);

        $prods = Product::all();// default all products to available
        $prods->each(function($prod) use ($instock) {
            $prod->availability()->associate($instock);
            $prod->save();
        });
    }
}
