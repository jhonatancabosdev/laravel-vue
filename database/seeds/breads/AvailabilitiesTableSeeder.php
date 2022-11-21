<?php

use App\Availability;
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
            'name' => 'Out of stock',
            'slug' => 'out-of-stock',
        ]);

        Availability::create([
            'name' => 'In stock',
            'slug' => 'in-stock',
        ]);

        Availability::create([
            'name' => 'Draft',
            'slug' => 'draft',
        ]);

        $availability = Availability::where('slug', 'out-of-stock')->first();
        $availability->id = 0;
        $availability->save();

        $availability = Availability::where('slug', 'in-stock')->first();
        $availability->id = 1;
        $availability->save();

        $availability = Availability::where('slug', 'draft')->first();
        $availability->id = 2;
        $availability->save();
    }
}
