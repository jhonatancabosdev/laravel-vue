<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecentProductsController extends Controller
{
   /**
     * Display a view for a visitor's recently viewed products
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return view('front.pages.recents');
    }
}