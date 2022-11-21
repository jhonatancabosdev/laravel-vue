<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $this->call([
            CountryTableSeeder::class,
            StateTableSeeder::class,
            CityTableSeeder::class,
            ZipcodeTableSeeder::class,
            CategoryTableSeeder::class,
            // Uses old CSV with innacurate taxonomy CategoryTableSeeder::class,
            ProductTableSeeder::class,
            ProductRelationSeeder::class,
            OrderStatusTableSeeder::class,
        ]);

        $env = env('APP_ENV');
        if($env !== 'production'){
            $this->call([
                ShippingTableSeeder::class,
                AvailabilitiesTableSeeder::class,
            ]);
        }
    }
}
