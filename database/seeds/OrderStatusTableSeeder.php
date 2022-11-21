<?php

use App\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderStatus::create([
        	'name' => 'new',
        ]);
        OrderStatus::create([
        	'name' => 'processed',
        ]);
        OrderStatus::create([
        	'name' => 'shipped',
        ]);
        OrderStatus::create([
        	'name' => 'cancelled',
        ]);
    }
}
