<?php

use App\Shipping;
use Illuminate\Database\Seeder;

class ShippingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shipping::create([
        	'name' => 'Ground',
			'description' => 'Shipping by land transport',
			'base_cost' => 250,
        ]);

    }
}
