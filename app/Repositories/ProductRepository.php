<?php

namespace App\Repositories;

use App\Product;
use App\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cookie;

class ProductRepository extends BaseRepository
{
    /**
     * Save cover
     *
     * @param UploadedFile $file
     *
     * @return false|string
     */
    public function saveCover(UploadedFile $file) 
    {
        return $file->store('products', ['disk' => 'public']);
    }

    /**
     * Save images
     *
     * @param Collection $collection
     * @param Product $product
     * @return void
     */
    public function saveProductImage(UploadedFile $file)
    {
        return $this->model->images()
            ->save(new ProductImage([
                'src' => $file->store('products', ['disk' => 'public']),
                'product_id' => $this->model->id
            ]));
    }


    /**
     * Get product with details
     */
    public function getProductWithDetails($product)
    {

        if(gettype($product) != 'object') {
            if((string)intval($product) == $product) {// Avoiding Type Coercion issue where slug like "6-month-supply" is parsed as "6" and found as an id.
                $product = Product::Where('id', $product)
                    ->firstOrFail();
            } else {
                $product = Product::Where('slug', $product)
                    ->firstOrFail();
            }
        }

        // make this product visited
        if( !Cookie::has( $product->id ) ){
            // create cookie for this product as visited
            Cookie::queue( Cookie::make($product->id, 1, 60) );
            // mark product as visited
            $product->increment('clicks_counter');
        }

        // This site does not do reviews at the moment, and it doesn't have product options.
        // $product->loadMissing(['images', 'categories', 'reviews', 'options.values.products', 'optionValues', 'parent', 'children.optionValues.products']);
        // $product->loadMissing(['images', 'categories']);

        return $product;
    }

    /**
     * Get Related Products for a given product
     */
    public function getRelatedProducts($product, $limit = 6)
    {
        /**
         * Related Products
         * 
         * First, products in the same category
         * Second, products in the same parent category
         * Third, all products
         * 
         */

        $relatedProducts = collect();

        foreach($product->categories as $category)
        {
            $relatedProducts = $relatedProducts->merge($category->products->loadMissing(['tags','categories']));
            
            // Probably a better way to check for other related products than the below
            // if($category->parent) {
            //     foreach($category->parent->children as $siblingCategory) {
            //         $relatedProducts = $relatedProducts->merge($siblingCategory->products);
            //     }
            // }
        }



        return $relatedProducts ? $relatedProducts->slice(0, $limit) : collect([]);
    }
    /**
     * filters a group of products by various request criteria in the request parameters
     * 
     * @param Request $request
     * @param Category $category
     * @param mixed $products
     * 
     * @return Collection $products
     * 
     */
    public function filterProducts(Request $request, $category = NULL, $products = NULL) {
        $tags = $request->get('tags');
        dd($tags);
        $products = $products ? $products : $category->products()->with(['images', 'brand'])->getResults();
        $brands = $products->pluck('brand')->flatten()->unique();
        // Filters
        $chosenBrands = $request->get('brand') ? explode(',', $request->get('brand')): FALSE;
        $chosenTypes = explode(',', $request->get('type'));
        $chosenProfessions = explode(',', $request->get('profession'));
        $chosenAvailability = $request->get('availability');
        $chosenPriceFrom = $request->get('price_from') == 0 ? NULL : $request->get('price_from') / 100;
        $chosenPriceTo = $request->get('price_to') == 0 ? NULL : $request->get('price_to') / 100;
        
        if($chosenBrands !== FALSE) {
            $filterBrands = Brand::whereIn('slug', $chosenBrands)->get();
            $products = $products->whereIn('brand', $filterBrands);
        }
        if($chosenPriceFrom !== NULL) {
            $products = $products->where('price','>=', $chosenPriceFrom);
        }
        if($chosenPriceTo !== NULL) {
            $products = $products->where('price','<=', $chosenPriceTo);
        }
        
        // if(count($chosenTypes) > 0) {
        //     $products = $products->whereIn('type', $chosenTypes);
        // }
        
        if($chosenAvailability === 'in-stock') {
            $products = $products->where('quantity','>', 0);
        }
            
        return $products;
    }
}
