<?php

use App\Imports\CategoryImport;
use App\Imports\CategoryDescriptionImport;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
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
      (new CategoryImport)->import(
        storage_path('app/public/imports/greenWorldCategories.csv'), 
        null, 
        \Maatwebsite\Excel\Excel::CSV
        );
    }
}