<?php

namespace App\Http\Controllers;

use Spatie\Tags\Tag;
use Illuminate\Http\Request;
use App\{ Category, Product };
use App\Repositories\ProductRepository;

class CategoryController extends Controller
{

    private $productRepo;

    public function __construct()
    {
        $this->productRepo = new ProductRepository();
    }
    
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
     * @param  mixed $category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $category)
    {
        if(gettype($category) != 'object') {
            $category = Category::where('id', $category)
                ->orWhere('slug', $category)
                ->firstOrFail();
        }

        $types = NULL;
        $professions = NULL;
     
        $viewType = $request->get('view_type') ?? 'grid';
        $paginationCount = $request->get('pagination_count') ? intval($request->get('pagination_count')) : 24;
         
        $categoryAncestors = Category::withDepth()->ancestorsAndSelf($category);
        
        $products = $category
            ->products()
            ->select('products.*')
            ->with(['images', 'tags'])
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->orderByRaw('CASE WHEN brands.slug = \'xerox\' THEN 0 ELSE 1 END')
            ->paginate();
        $category->children->each(function($subCategory) use (&$products){
            $products = $products->merge($subCategory->products);
        });
        $products->loadMissing(['images', 'tags']);

        $tags = [];

        // Tag Types need to be defined by Category

        $products->each(function($prod) use (&$tags){
            $types = $prod->tags->pluck('type')->unique();
            foreach($types as $type) {
                $typedTag = $prod->tagsWithType($type)->first();
                if (!isset($tags[$type])) {
                    $tags[$type] = array($typedTag);
                } else {
                    if(!in_array($typedTag, $tags[$type])) {
                        $tags[$type][] = $typedTag;
                    }
                }
            }
        });

        foreach($tags as &$type) {
            $uniques = [];
            foreach($type as $tag) {
                $uniques[$tag->name] = $tag;// Make sure that only unique values appear in the list
            }
            ksort($uniques);// Sort all Filters by their name

            $type = $uniques;
        }
        
        $topPrice = $request->get('maxPrice') ?? $products->max('price');
        $bottomPrice = $request->get('minPrice') ?? $products->min('price');
        
        $maxPrice = $products->max('price');
        $minPrice = $products->min('price');
        
        $products = $products->whereBetween('price', [$bottomPrice, $topPrice])->loadMissing('tags')->paginate();
        
        $categoryFamily = $category->with(['descendants', 'products'])->get();
        $categoryAncestors = $categoryFamily->pluck('id');
        
        
        // $brands = $products->pluck('brand')->flatten()->unique();
        $sortBy = $request->get('sort_by') ?? 'relevance';
        
        if($sortBy === 'l-t-h') {
            $products = $products->sortBy('price');
        } elseif($sortBy === 'h-t-l'){
            $products = $products->sortByDesc('price');
        } else {
            $products = $products->sortByDesc('sales_count');
        }
        
        $products = $products->loadMissing(['tags','categories'])->paginate(20);

        return view('front.categories.list', [
            'categoryAncestors'     => $categoryAncestors,
            'category'              => $category,
            'currentCategory'       => $category,// Used in breadcrumb to avoid variable naming collisions
            // 'brands'                => $brands,
            'types'                 => $types,
            'professions'           => $professions,
            'products'              => $products,
            'viewType'              => $viewType,
            'paginationCount'       => $paginationCount,
            'sortBy'                => $sortBy,
            'maxPrice'              => floatVal($maxPrice),
            'minPrice'              => floatVal($minPrice),
            'categoryPage'          => TRUE,
            'tags'                  => $tags
        ]);

    }

    /**
     * A method that takes a request with filter parameters
     * and returns a jsonResponse of products that match
     */
    public function filterCategory(Request $request, $category)
    {
        
        $cat = Category::where('slug', $category)->orWhere('id', $category)->first();

        $ids = [$cat->id];

        if($cat->parent === null) {
            $ids = $cat->children->pluck('id')->merge($ids);
        }

        
        $maxPrice = 1000;
        $minPrice = 0;

        $query = Product::select('products.*')->whereHas('categories', function($q) use ($cat, $ids) {
            $q->whereIn('categories.id', $ids);
        });

        $tags = [];
        
        foreach($request->query() as $param => $value) {// Loop through filters
            if(substr($param, 0, 4) == 'tag-' ) {
                $tagGrp = str_replace('-',' ', substr($param, 4));
                $values = explode('**',$value);
                if(count($values) > 0) {
                    foreach($values as &$val) {
                        if($val) {
                            $val = Tag::where('type', 'like', '%' . $tagGrp . '%')->where('slug->en', $val)->get();
                            $val = $val->pluck('name')[0];
                        }
                    }
                    if(strpos($param, 'tag-') === 0) {
                        $tags[$tagGrp] = $values;
                    }
                    $query = $query->withAnyTags($tags[$tagGrp], $tagGrp)->with('tags');
                }
            }
        }

        $query->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->orderByRaw('CASE WHEN brands.slug = \'xerox\' THEN 0 ELSE 1 END');

        $prods = $query->get();

        if($prods) {
            $max = $prods->max('price');
            $maxPrice = $prods->max('price') + 50;
        } else {
            $max = 1500;
            $maxPrice = 1550;
        }
        $min = 0;

        $topPrice = $request->get('maxPrice') ?? $max;
        $bottomPrice = $request->get('minPrice') ?? $min;
                
        $products = $query->whereBetween('products.price', [$bottomPrice, $topPrice])->paginate($request->get('perPage', 24));

        $products->each(function(&$prod){
            $prod = $prod->loadMissing(['tags','categories']);
        });

        return response()->json([
            'parentCategories'  => $cat->parent,
            'navItems'          => $cat->children ?? [],
            'products'          => $products,
            'category'          => $category,
            'topPrice'          => $topPrice,
            'bototmPrice'       => $bottomPrice,
            'maxPrice'          => $maxPrice,
            'minPrice'          => $minPrice,
        ]);
    }
}
