<?php

namespace App\Imports;

use App\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class CategoryImport implements ToCollection
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $total = 0;
        $errors = 0;

        // BEGIN Creating the main nav bar top items as categories from Col 0 in csv**************
        $rows->each(function ($row, $index) use (&$total, &$errors, &$greatGrand, &$grand, &$parent, &$child) {

            try {
                if ($row[0] != null){
    

                    $greatGrand = $row[0];
                    
                    Category::firstOrCreate([
                        'slug'                => Str::slug(ltrim($row[0])),
                        'name'                => ltrim($row[0]),
                        'on_navbar'                         => 1,
                        // 'homepage_order'            => $order,
                    ]);
                        
                }
                if ($row[1] != null){
                    $grand = $row[1];


                    $directParent = Category::where('name', $greatGrand)->first(); 
                    Category::firstOrCreate([
                        'slug'                => Str::slug(($greatGrand) . " " . $row[1]),
                        'name'                => $row[1],
                        'parent_id'                         => $directParent->id,
                        'on_navbar'                         => 1,
                        // 'homepage_order'            => $order,
                    ]);
                
                }

            } catch (\Exception $e) {
                $errors++;
                logger(Str::limit($e->getMessage(), 150));
            }

        });
    }
}