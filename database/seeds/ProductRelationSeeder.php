<?php

use App\{ Category, Product };
use Illuminate\Database\Seeder;
use App\Imports\ProductRelationImport;

class ProductRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new ProductRelationImport)->import(
            storage_path('app/public/imports/greenWorldProductsCategories.csv'), 
            null, 
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
