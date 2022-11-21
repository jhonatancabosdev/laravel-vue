<?php

namespace App\Http\Controllers;

use App;
use App\Product;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index(Request $request)
    {
        // create new sitemap object
        $sitemap = App::make('sitemap');
    
        // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
        // by default cache is disabled
        $sitemap->setCache('laravel.sitemap', 60);
    

        // get all products from db
        $products = Product::all()->sortBy('created_at');
        // get all categories from db
        $categories = Category::all()->sortBy('created_at');

        // check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached()) {
            // add item to the sitemap (url, date, priority, freq)
            $sitemap->add(URL::to('/'), Carbon::now(), '1.0', 'daily');

            // add every product to the sitemap
            foreach ($products as $product) {
                if($product->updated_at !== NULL) {
                    $lastMod = $product->updated_at->tz('UTC')->toAtomString();
                } elseif($product->created_at !== NULL) {
                    $lastMod = $product->created_at->tz('UTC')->toAtomString();
                } else {
                    $lastMod = Carbon::now();
                }
                $sitemap->add(URL::to('product/' . $product->slug), $lastMod, '0.9', 'monthly');
            }

            // add every category to the sitemap
            foreach ($categories as $category) {
                if($category->updated_at !== NULL) {
                    $lastMod = $category->updated_at->tz('UTC')->toAtomString();
                } elseif($category->created_at !== NULL) {
                    $lastMod = $category->created_at->tz('UTC')->toAtomString();
                } else {
                    $lastMod = Carbon::now();
                }
                $sitemap->add(URL::to('category/' . $category->slug), $lastMod, '0.9', 'monthly');
            }
        }

        $rendering = $sitemap->render('xml');

        if(strtolower($request->get('generate')) == 'true') {
            $sitemap->store('xml', 'sitemap');
        }
    
        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $rendering;
        //return dd($products->first()->updated_at->tz('UTC')->toAtomString());
        
        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        $rendering = $sitemap->render('xml');

        if($request->get('generate') == 'true') {
            $sitemap->store('xml', 'sitemap');
        }
    
        return $rendering;
        // return response()->view('sitemap')->header('Content-Type', 'text/xml');
    }
}
