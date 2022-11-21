<?php

namespace App\Http\Controllers;

use App\Rules\IsEmpty;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\CartItem;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\{ Request, Response };
use App\{ Brand, Category, Product, Review };
use App\Repositories\{ ProductRepository, ReviewRepository };

class ProductController extends Controller
{
    private $productRepo;
    private $reviewRepo;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->productRepo = new ProductRepository();
        $this->reviewRepo = new ReviewRepository(new Review());
    }

    /**
     * Special search for the homepage-only search by Category and Brand
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function categoryBrandSearch(Request $request)
    {
        if($request->input('search_category') === NULL && $request->input('search_brand')  === NULL) {
            return back()->with('error', 'please pick a category and/or brand to search for');
        }
        $searchCat = $request->input('search_category');
        $searchBrand = $request->input('search_brand');
        $category = Category::where('id', $searchCat)->first();
        $brand = Brand::where('id', $searchBrand)->first();

        $categoryChilds = [];

        if($category === NULL) {// Looking for a brand without a specific category
            $products = Product::
                whereHas('brand', function ($q) use ($searchCat, $searchBrand) {
                    return $q->where('id', $searchBrand);
                })
                ->with(['brand'])
                ->get();
        } elseif ($brand === NULL) {// Looking for a category without a specific brand
            if($category->children) {
                $cats = $category->children->pluck('id');
                $cats[] = $category->id;
            }
            $products = Product::
                whereHas('categories', function ($q) use ($cats) {
                    return $q->whereIn('id', $cats);
                })
                ->with(['categories'])
                ->get();

            $categoryChilds = $category->children->map(function ($item) {
                $item->depth += 1;
                return $item->only(['slug', 'name', 'depth']);
            });
        
        } else {// Looking for both
            $products = Product::
                whereHas('brand', function ($q) use ($searchCat, $searchBrand) {
                    return $q->where('id', $searchBrand);
                })
                ->whereHas('categories', function ($q) use ($searchCat, $searchBrand) {
                    return $q->where('id', $searchCat);
                })
                ->with(['brand', 'categories'])
                ->get();

            $categoryChilds = $category->children->map(function ($item) {
                $item->depth += 1;
                return $item->only(['slug', 'name', 'depth']);
            });
    
        }
    
        $products = $products->loadMissing(['tags','brand'])->paginate();

        $parentCategories = Category::remember(config('default-variables.cache_life_time'))
            ->ancestorsAndSelf($category);

        $viewType = $request->get('view_type') ?? 'grid';

        return view('front.categories.list', [
            'parentCategories'  => $parentCategories,
            'navItems'          => $categoryChilds ?? [],
            'products'          => $products,
            'brand'             => $brand,
            'category'          => $category,
            'viewType'          => $viewType,
            'search'            => TRUE
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        if(trim($request->input('keyword')) === '') {
            return redirect()->back();
        }

        $query = Product::select('products.*')->whereHas('categories')
            // ->orderByDesc('name')
            ->when(intval($request->input('priceFrom')) !== 0, function($query) use ($request) {
                return $query->where('price', '>', floatval($request->input('priceFrom')));
            })
            ->when(intval($request->input('priceTo')) !== 0, function($query) use ($request) {
                return $query->where('price', '<', floatval($request->input('priceTo')));
            })
            ->with(['images', 'brand', 'tags'])
            ->where('quantity', '>', 0);

        $query = ! ($request->has('ids') && is_array($request->input('ids')))
            ? $query->search($request->input('keyword')) 
            : $query->whereIn('id', $request->input('ids'));

        switch ($request->get('sortBy', 'l-t-h')) {

            case 'l-t-h':
                $query = $query->orderBy('price', 'asc');
                break;

            case 'h-t-l':
                $query = $query->orderBy('price', 'desc');
                break;
        }

        $products = $query->paginate($request->get('perPage', 24));

        try {
            $category = $products->first()->categories->first();
            $categoryChilds = $category->children->map(function ($item) {
                $item->depth += 1;
                return $item->only(['slug', 'name', 'depth']);
            });
        } catch (\Exception $e) {
            $category = null;
        }

        $parentCategories = Category::remember(config('default-variables.cache_life_time'))
            ->ancestorsAndSelf($category);

        $parentCategoriesIds = $parentCategories->pluck('id')->all();

        $viewType = $request->get('view_type') ?? 'grid';

        if (! $request->ajax()) {
            return view('front.categories.list', [
                'parentCategoriesIds'   => $parentCategoriesIds,
                'category'              => $category,
                'products'              => $products,
                'viewType'              => $viewType,
                'search'                => $request->input('keyword')
            ]);
        }

        $maxPrice = floatval($products->max('price'));
        $minPrice = floatval($products->min('price'));

        return response()->json([
            'parentCategories' => $parentCategories,
            'navItems' => $categoryChilds ?? [],
            'products' => $products,
            'category' => $category,
            'maxPrice' => $maxPrice,
            'minPrice' => $minPrice,
        ]);
    }

    /**
     * Filter products
     * 
     * @param  Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filter(Request $request)
    {
        $products = Product::where('status',1)->get();
        $products = $this->productRepo->filterProducts($request, NULL, $products)->paginate();

        return view('front.categories.list', [
            'products' => $products->paginate()
        ]);
    }
    /**
     * Get the product
     *
     * @param Mixed $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($product)
    {
        $product = $this->productRepo->getProductWithDetails($product);

        $tags = $product->tags->pluck('name', 'type');
        
        // This site does not use reviews
        // $reviews = $product->reviews;
        // $averageRating = $reviews->avg('grade') ?? 0;

        // This site does not have child products
        /* $relevantOptionSets = [];
        foreach($product->children as $child) {
            foreach($child->optionValues as $value) {
                if(!array_key_exists($value->option->name, $relevantOptionSets)) {
                    $relevantOptionSets[$value->option->name] = [
                        'id'        => $value->option->id,
                        'slug'      => $value->option->slug,
                        'values'    => []
                    ];
                }
                if(!in_array($value->id, array_keys($relevantOptionSets[$value->option->name]['values']))) {
                    $relevantOptionSets[$value->option->name]['values'][$value->id] = $value;
                }
            }
        } */
        
        $currentCategory = $product->categories->first();

        $categoryAncestors = Category::withDepth()->ancestorsAndSelf($currentCategory)->pluck('id')->all();

        $relatedProducts = $this->productRepo->getRelatedProducts($product, 3);

        $viewType = NULL;

        // This site does not have parent/child products
        // $accessibleChildren = $product->children()->with('optionValues')->has('optionValues')->get();

        return view(
            'front.products.show',
            compact(
                // 'relevantOptionSets',
                'categoryAncestors',
                'currentCategory',
                'product',
                // 'accessibleChildren',
                'relatedProducts',
                //'reviews',
                'viewType',
                // 'averageRating'
                )
        );
    }

    /**
     * Related Products View
     * TODO, give more arguments to change what algorithm to use for related products
     *
     * @param Mixed $product
     *
     * @return \Illuminate\View\View|string
     */
    public function relatedProducts($product)
    {
        $product = $this->productRepo->getProductWithDetails($product);

        // $relatedProducts = $this->productRepo->getRelatedProducts($product)->paginate(3);
        $relatedProducts = $this->productRepo->getRelatedProducts($product);

        if(count($relatedProducts) == 0){
            return '';
        } else {
            return view('front.products.related', compact('relatedProducts'));
        }

    }

    /**
     * Product Quick View
     * TODO, give more arguments to change what algorithm to use for related products
     *
     * @param Mixed $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function quickView($product)
    {
        $product = $this->productRepo->getProductWithDetails($product);

        return view('front.products.quick-view', compact('product'));
    }

    /**
     * Add a review to a product
     * 
     */

     public function review(Request $request, Product $product){
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'grade' => 'required',
            'title' => 'required',
            'email_address' => 'required',
            'review_dont_fill' => [new IsEmpty],
        ]);

        $data = $request->except('_token');
        $data['grade'] = (float) $request['grade'];

        $review = $this->reviewRepo->createReview($data);

        return $review;
     }
}
