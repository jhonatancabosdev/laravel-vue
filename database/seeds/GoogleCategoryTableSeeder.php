<?php

use App\Imports\GoogleCategoryImport;
use Illuminate\Database\Seeder;

class GoogleCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // (new CategoryDescriptionImport)->import(
        // storage_path('app/public/imports/PacificSands.csv'), 
        // null, 
        // \Maatwebsite\Excel\Excel::CSV
        // );
      (new GoogleCategoryImport)->import(
        storage_path('app/public/imports/googleCatTaxonomy.csv'), 
        null, 
        \Maatwebsite\Excel\Excel::CSV
        );
    }
}