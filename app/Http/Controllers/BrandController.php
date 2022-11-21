<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Http\Request;
use App\Repositories\BrandRepository;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        $products = $brand->products()->with(['images','tags','categories'])->paginate();

        $brands = Brand::whereHas('products')
        ->get();

        return view('front.brands.list', [
            'brand'     => $brand,
            'brands'    => $brands,
            'products'  => $products,
            'brandPage' => TRUE
        ]);
    }
}
