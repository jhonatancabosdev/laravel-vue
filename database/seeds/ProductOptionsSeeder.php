<?php

use Faker\Provider\Lorem;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\{ Product, Option, OptionValue };

class ProductOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $optionNames = ['size-signal range', 'size', 'accessories', 'model', 'parts', 'name'];
        // $allOptions = an array of random option names between 2,10 items in length
        // $allOptions = Lorem::words(mt_rand(2, 10));
        
        // Create a bunch of option sets
        Option::insert(array_map(function($name){
            return [
                'name'  => $name,
                'slug'  => Str::slug($name)
            ];
        }, $optionNames));

        $allOptions = Option::all();
        
        $parentProducts = Product::all();
        
        foreach($parentProducts as $parent) {// give each product a collection of children
            // Set a random number of children for each product - between 0 and 10 children
            $childCount = mt_rand(0, 10);

            $children = factory(App\Product::class, $childCount)->create();
            // Create $childCount children and mark them as children for the product
            $parent->children()->saveMany($children);
            // create a random number of optionsets between 0 and 2 that will be created for this parent
            $numberOfOptSets = mt_rand(1,2);
        
            for($i = 0; $i < $numberOfOptSets; $i++) {// loop until the optsets are created and populated with values            
                
                $optionName = $optionNames[array_rand($optionNames)];// Create an option name

                if($parent->options->count() !== 0){
                    while($parent->options->pluck('name')->contains($optionName)) {// Check to see if the option is already on the product
                        $optionName = $allOptions[array_rand($allOptions)];// Pick a different option name 
                    }
                }

                // Create a new option to link to the parent product
                $option = Option::where('name', $optionName)->first();
                if(!$option->products->contains($parent)) {
                    $option->products()->attach($parent);
                }

                // each option needs to have a set of optionvalues
                $numberOfVals = mt_rand(1, 5);
                $values = Lorem::words($numberOfVals);

                foreach($children as $child) {
                    if(mt_rand(0,2) > 0) {// for SOME children pick an optionvalue to apply
                        $optionValueName = $values[mt_rand(0, $numberOfVals - 1)];
                        
                        // find or create an OptionValue that is linked to the optionset
                        $optVal = OptionValue::firstOrNew([
                            'name'      => $optionValueName,
                            'slug'      => Str::slug($optionValueName),
                            'option_id' => $option->id
                        ]);
                        $optVal->save();

                        // link the OptionValue to $newOption and the current child product $currentChil
                        $optVal->products()->attach($child);
                        $optVal->option()->associate($option);
                        $optVal->save();
                    }
                }
            }

        }
    }
}