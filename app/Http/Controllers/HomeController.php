<?php

namespace App\Http\Controllers;

use App\Order;
use App\Mail\OrderMailable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\{ Category, Brand, Product };

class HomeController
{
    /**
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $categories = Category::where('parent_id', null)->get();
        $products = Product::where('homepage_order', '<>', NULL)->orderBy('homepage_order')->orderBy('name')->where('parent_id', NULL)->get();
        $brands = Brand::whereNotNull('cover')->get();
        $isHome = TRUE;

        return view('front.home', compact('isHome', 'products','categories', 'brands'));
    }

    public function companyInformation(){
        return view('front.pages.company-information');
    }

    public function test(){

        $order = Order::confirmed()->first();

        return view('front.checkout.success', [
            'order' => $order
        ]);
    }

    public function faq(){
        return view('front.pages.faq');
    }

    public function vision(){
        return view('front.pages.vision');
    }

    public function privacyPolicy(){
        return view('front.pages.privacy-policy');
    }

    public function terms(){
        return view('front.pages.terms-and-conditions');
    }

    public function returns(){
        return view('front.pages.returns');
    }

    public function refunds(){
        return view('front.pages.refunds');
    }

    public function aboutUs(){
        return view('front.pages.about-us');
    }

    public function contactUs(){
        return view('front.pages.contact-us');
    }

    public function shippingPolicy(){
        return view('front.pages.shipping-policy');
    }

    public function fallback(){
        return redirect(route('home'));
    }
}
