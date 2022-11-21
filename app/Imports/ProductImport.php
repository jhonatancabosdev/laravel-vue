<?php

namespace App\Imports;
use File, Storage;
use Spatie\Tags\Tag;
use App\{ Product, ProductImage, Brand, Category };
use Illuminate\Support\{ Str, Collection };
use Maatwebsite\Excel\Concerns\{ Importable, ToCollection };

class ProductImport implements ToCollection
{
    use Importable;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $collection->each(function($row, $index){

        	if ($index < 1) {
        		return true;
			}


        	// L x W x H
        	$length = $row[12] > 0 ? $row[13] : 8;
        	$width = $row[13] > 0 ? $row[14] : 8;
        	$height = $row[14] > 0 ? $row[15] : 8;
			
			if($row[12] > 0){
				$weight = $row[12];
			}else{
				$weight = 1;
			}

			if($row[11] != null){
				$price = $row[11];
			}else{
				$price = 29.99;
			}
			
			$comparePrice = $index % 2 ? $price + $price * .3 : NULL;

			$slug = Str::slug($row[1]);

        	$product = Product::create([
        		'name' 				=> $row[1],
        		'slug' 				=> $slug,
        		'sku' 				=> $row[5],
        		// 'product_id' 	=> $row[3],
        		'mpn' 				=> $row[5],// Default to SKU	
				'quantity' 			=> $row[10],
				'weight'			=> $weight,
				'length'			=> $length,
				'width'				=> $width,
				'height'			=> $height,
				'price' 			=> $price,
				'original_price' 	=> $comparePrice,
				'compare_at_price' 	=> $comparePrice,
				'description' 		=> $row[4],
				'short_description' => $row[2],
        	]);
			
			$slugName =  Str::slug($row[3]);
			
			/****** Brand *******/

			$publicBrandsImgPath = public_path('images/brands/' . $slugName . '.png');

			$brandsPath = storage_path('app/public/brands');

			if(!File::exists($brandsPath)) {
				File::makeDirectory($brandsPath, $mode = 0775, true, true);
			}

			$storageBrandsImgPath = $brandsPath . '/' . $slugName . '.png';

			if(!File::exists($storageBrandsImgPath) && File::exists($publicBrandsImgPath)) {
				File::copy($publicBrandsImgPath, $storageBrandsImgPath);
			}
			if(File::exists($storageBrandsImgPath)) {
				$dbBrandsImgPath = 'brands/' . $slugName . '.png';
			} else {
				$dbBrandsImgPath = NULL;// Default to NULL
			}
			
			$brand = Brand::firstOrNew([
				'name'	=> $row[3],
				'slug'	=> $slugName,
				'cover' => $dbBrandsImgPath
			]);
			$brand->save();

			$brand->products()->save($product);

			/****** Product Image *******/

			$imgSrc = '0000' . $row[134] . '_0.jpeg';

			$publicProductsImgPath = public_path('images/products/' . $imgSrc);

			$productsPath = storage_path('app/public/products');

			if(!File::exists($productsPath)) {
				File::makeDirectory($productsPath, $mode = 0775, true, true);
			}

			$storageProductsImgPath = $productsPath . '/' . $imgSrc;

			if(!File::exists($storageProductsImgPath) && File::exists($publicProductsImgPath)) {
				File::copy($publicProductsImgPath, $storageProductsImgPath);
			}

			if(File::exists($storageProductsImgPath)) {
				$dbProductsImgPath = 'products/' . $imgSrc;
				$productImage = ProductImage::create([
				   'product_id'  => $product->id,
				   'src'     => $dbProductsImgPath,
				   'is_main'   => 1
				]);
				
				$productImage->product()->associate($product);
			}	

			/**
			 * Loop through the the product attribute values,
			 * if there is a value, tag the product with a grouped tag with the tag = $item[0] and group = $item[1]
			 */
			$refinementAttrs = [
				[$row[20], $row[27]],
				[$row[43], $row[50]],
				[$row[66], $row[73]],
				[$row[89], $row[96]],
				[$row[112], $row[119]]
			];

			foreach($refinementAttrs as $attr) {
				if($attr[1]) {
					if( $attr[0] == 'Manufacturer Part #' || $attr[0] == 'Manufacturer Part#' ) {
						$attr[0] = 'Manufacturer Part Num';
					}
					if( $attr[1] == 'Manufacturer Part #' || $attr[1] == 'Manufacturer Part#' ) {
						$attr[1] = 'Manufacturer Part Num';
					}
					if($attr[0] == 'Weight') {
						if($attr[1] == '1') {
							$attr[1] = $attr[1] . ' lb';
						} else {
							$attr[1] = $attr[1] . ' lbs';
						}
					}
					$productTag = Tag::findOrCreate($attr[1], $attr[0]);
					$product->attachTag($productTag);
					$product->save();
				}
			}

		});

		if(config('app.env') === 'local') {// For testing purposes, feature a few prods on the homepage
			$prods = Product::orderByRaw('RAND()')->take(5)->get();

			$iter = 1;
			$prods->each(function($prod) use (&$iter) {
				$prod->homepage_order = $iter;
				$prod->save();
				$iter++;
			});
		}

    }
}
