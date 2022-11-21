<?php

namespace App\Imports;

use App\GoogleCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class GoogleCategoryImport implements ToCollection
{
    use Importable;

     function setGoogleCat($index, $row){
         $parentID = null;
        if($row[$index + 1] == null){
            $parent = GoogleCategory::where('name', $row[$index - 1])->first();
            if($parent != null){
                $parentID = $parent->id;
            }
        }
        if($row[$index + 1] != null && $index + 1 < 7){
            GoogleCategoryImport::setGoogleCat($index + 1, $row);
        }else{
            GoogleCategory::create([
                'slug'                => Str::slug($row[$index]),
                'name'                => $row[$index],
                'id'                         => $row[0],
                'parent_id'                => $parentID,
            ]);      
        }  
    }
    
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
        $rows->each(function ($row, $index) use (&$total, &$errors) {
            if ($index < 1) {
                return;
            }
            GoogleCategoryImport::setGoogleCat(0, $row);
        });
// END Creating the main nav bar top items as categories from Col 0 in csv**************
        logger($total);
        logger($errors);
    logger($rows->count());
    }
}