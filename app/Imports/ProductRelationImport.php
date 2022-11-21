<?php

namespace App\Imports;

use App\Product;
use App\Category;
use App\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductRelationImport implements ToCollection
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


			if ($index == 0) {
        		return true;
        	}
            try {
                if ($row[0] != null){
					$primaryCategories = explode(',', $row[2]);

					$product = Product::where('name', $row[1])->first(); 
					$parent = Category::where('name', $row[2])->first();
					if($row[3] != null && $row[3] != '') {
						$categories = Category::where('name', $row[3])->get();
						foreach($categories as $cat){
							if($cat->parent_id == $parent->id){
								$category = $cat;
							}
						}
						
						$product->categories()->save($category);
					} else {
						foreach($primaryCategories as $value){ 
							$category = Category::where('name', ltrim($value))->first();
							$product->categories()->save($category);
						}
					}       
				}
            } catch (\Exception $e) {
                $errors++;
                logger(Str::limit($e->getMessage(), 150));
            }

        });
    }
}
