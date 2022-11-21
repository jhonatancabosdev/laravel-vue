<?php

use App\Product;
use Faker\Generator as Faker;
use App\Imports\{ ProductImport, ProductIdImport };
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new ProductImport)->import(
            storage_path('app/public/imports/products_new.csv'),
            null, 
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
