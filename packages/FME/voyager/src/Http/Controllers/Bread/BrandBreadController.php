<?php

namespace TCG\Voyager\Http\Controllers\Bread;

use App\Brand;
use App\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class BrandBreadController extends VoyagerBaseController
{
    /**
     * @param  Request $request
     * @param  Product $product
     * @return Response
     */
    public function storeAndAttach(Request $request, Product $product)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $product->brand()->associate(Brand::firstOrCreate([
            'name' => $request->input('brand'),
            'slug' => Str::slug($request->input('brand'))
        ]))
        ->save();

        return $product;
    }
}
