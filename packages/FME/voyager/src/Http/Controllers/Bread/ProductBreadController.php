<?php

namespace TCG\Voyager\Http\Controllers\Bread;

use App\Brand;
use App\Product;
use App\Category;
use App\Availability;
use App\ProductImage;
use App\CategoryProduct;
use Illuminate\Support\Arr;
use App\Scopes\EnabledScope;
use Illuminate\Http\Request;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Tags\Tag;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Http\Controllers\ContentTypes\MultipleImage;
use TCG\Voyager\Http\Controllers\ContentTypes\Image as ContentImage;

class ProductBreadController extends VoyagerBaseController
{
    /**
     * @return View
     */
    public function index(Request $request)
    {
        $this->authorize('browse', app(Product::class));

        $availabilities = Availability::all();

        $dataType = Voyager::model('DataType')->where('slug', $this->getSlug($request))->first();

        $actions = [];

        foreach (Voyager::actions() as $action) {
            $action = new $action($dataType, Product::class);

            if ($action->shouldActionDisplayOnDataType()) {
                $actions[] = $action;
            }
        }

        if (! $request->ajax()) {
            $view = view()->exists("voyager::products.browse") ? 'voyager::products.browse' : 'voyager::bread.browse';
            return Voyager::view($view, [
                'brands' => \App\Brand::all(),
                'categories' => \App\Category::get(),
                'actions' => $actions,
                'dataType' => $dataType,
                'availabilities' => $availabilities
            ]);
        }

        if (session()->has('admin_page')) {
            session()->forget('admin_page');
        }

        $query = $this->prepareFilterQuery($request);

        $dataTypeContent = $query->withoutGlobalScopes()->whereNull('deleted_at')->paginate(30);

        $dataTypeContent->transform(function ($product) {
            $product->nested_type = $product->nested_type;
            return $product;
        });

        $responseData = compact('actions', 'dataType', 'dataTypeContent', 'availabilities');

        return $dataTypeContent;
    }

    /**
     * @return Builder
     */
    protected function prepareFilterQuery(Request $request)
    {
        $query = Product::with(['children', 'categories', 'brand', 'parent', 'images', 'availability']);

        $search = (object) ['value' => $request->get('s')];

        $orderBy = $request->get('order_by', 'id');
        $sortOrder = $request->get('sort_order', 'asc');
        $sortOrder = ! in_array(strtolower($sortOrder), ['asc', 'desc']) ? 'asc' : $sortOrder;

        if ($search->value !== null && trim($search->value) !== '' && trim($request->get('s')) !== '') {
            $query = $query->search($search->value);
        }

        if ($orderBy === 'children') {
            $query = $query->orderBy('parent_id', $sortOrder);
        } elseif ($orderBy === 'product_belongstomany_category_relationship') {
            $query = $query->orderBy(
                CategoryProduct::select('category_id')->whereColumn('products.id', 'product_id')->orderBy('category_id', 'desc')->limit(1),
                $sortOrder
            );
        } elseif ($orderBy === 'product_belongsto_brand_relationship') {
            $query = $query->orderBy(
                Brand::select('name')
                        ->whereColumn('products.brand_id', 'id')
                        ->orderBy('id', 'desc'),
                $sortOrder
            );
        } elseif ($orderBy === 'product_hasmany_product_image_relationship') {
            $query = $query->orderBy(
                ProductImage::select('name')
                        ->whereColumn('id', 'product_images.product_id')
                        ->orderBy('id', 'desc'),
                $sortOrder
            );
        } else {
            $query = $query->orderBy($orderBy, $sortOrder);
        }

        if ($request->input('status') && is_array($request->input('status'))) {
            $query = $query->whereIn('status', $request->input('status'));
        }

        if ($request->input('availability_id') && is_array($request->input('availability_id'))) {
            $query = $query->whereIn('availability_id', $request->input('availability_id'));
        }

        if ($request->input('images') && is_array($request->input('images'))) {
            $query = $query->where(function ($q) use ($request) {
                if (in_array('no', $request->input('images'))) {
                    $q = $q->whereDoesntHave('images');
                }

                if (in_array('yes', $request->input('images'))) {
                    $q = $q->has('images');
                }
            });
        }

        if ($request->input('family') && is_array($request->input('family'))) {
            $query = $query->where(function ($q) use ($request) {
                if (in_array('standalone', $request->input('family'))) {
                    $q = $q->whereDoesntHave('children');
                }

                if (in_array('child', $request->input('family'))) {
                    $q = $q->has('parent');
                }

                if (in_array('parent', $request->input('family'))) {
                    $q = $q->has('children');
                }
            });
        }

        return $query;
    }

    /**
     * @param  Request $request
     * @param  int  $id
     * @return response
     */
    public function edit(Request $request, $id)
    {
        if ($request->has('page') && (int) $request->input('page') !== 0) {
            session(['admin_page' => (int) $request->input('page')]);
        }

        return parent::edit($request, $id);
    }


    /**
     * @param  Request $request
     * @param  int  $id
     * @return response
     */
    public function show(Request $request, $id)
    {
        if (session()->has('admin_page') && (int) session('admin_page') !== 0) {
            return redirect()->route('voyager.products.edit', [
                $id, 'page' => (int) session('admin_page')
            ]);
        }

        return redirect()->route('voyager.products.edit', $id);
    }

    /**
     * @param  Request $request
     * @return response
     */
    public function store(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $request->validate([
            'name' => 'required|min:3'
        ]);

        $response = parent::store($request);

        if (! $response->getSession()->has('data')) {
            return $response;
        }

        $product = $response->getSession()->get('data');

        $product->categories()->sync(
            Category::find($request->input('selected_categories'))
        );

        return $this->handleProductAdditionalUpdate($response, $request, $product);
    }

    /**
     * @param  Request $request
     * @param  int  $id
     * @return response
     */
    public function update(Request $request, $id)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $request->validate([
            'name' => 'required|min:3',
            'tags' => 'array'
        ]);

        $product = Product::withoutGlobalScopes()->findOrFail($id);

        $response = parent::update($request, $product);

        return $this->handleProductAdditionalUpdate($response, $request, $product) ?? redirect()->back();
    }

    /**
     * Update additional attributes
     *
     * @param  Response $response
     * @param  Request $request
     * @param  Product  $product
     * @return void
     */
    private function handleProductAdditionalUpdate($response, Request $request, Product $product)
    {
      
        if($request->feature_on_homepage) {
            $product->homepage_order = $request->homepage_order;
        } else {
            $product->homepage_order = NULL;
        }

        $product->width = $request->input('width', 0);
        $product->height = $request->input('height', 0);
        $product->length = $request->input('length', 0);
        
        $product->weight = $request->input('weight', 0);

        $product->quantity = $request->input('quantity', 0);
        $product->upc = $request->input('upc', null);

        $id = $product->id;
        $product->status = $request->input('status', 0) === 'on' ? 1 : $request->input('status', 0);
        $product->is_map_enabled = $request->input('is_map_enabled', 0) === 'on' ? 1 : $request->input('is_map_enabled', 0);
        $product->is_on_feed = $request->input('is_on_feed', 0) === 'on' ? 1 : $request->input('is_on_feed', 0);
        $product->original_price = $request->input('original_price', 0);
        $product->map_price = $request->input('map_price', 0);
        $product->cost = $request->input('cost', 0);
        $product->price = $request->input('price', 0);
        $product->compare_at_price = $request->input('compare_at_price', $product->price + 4);

        if ($request->input('relation_type') === 'standalone') {
            $product->parent_id = null;
            $product->children()->update(['parent_id' => null]);
        }

        $product->availability_id = $request->input('availability_id', 0);
        $product->save();

        $tags = collect();

        foreach($request->tags ?? [] as $key => $value){
            if($key and $value){
                $tags->push(Tag::findOrCreate($value, $key));
            }
        }
        
        $product->syncTags($tags);

        if (! ($response->getSession()->has('alert-type') && $response->getSession()->get('alert-type') === 'success')) {
            return $response;
        }

        $slug = $this->getSlug($request);
        $row = new DataRow();

        try {
            $row->field = 'images';
            $images = (new MultipleImage($request, $slug, $row, []))->handle();

            foreach (Arr::wrap(json_decode($images)) as $src) {
                ProductImage::create([
                    'product_id' => $id,
                    'src' => $src,
                ]);
            }
        } catch (\Exception $e) {
        }

        try {
            $file = $request->file('main_image');
            $row->field = 'main_image';

            if (! $file) {
                throw new \Exception("No main image uploaded");
            }

            ProductImage::where('product_id', $id)->update([
                'is_main' => false,
            ]);

            ProductImage::create([
                'product_id' => $id,
                'is_main' => true,
                'src' => (new ContentImage($request, $slug, $row, []))->handle(),
            ]);
        } catch (\Exception $e) {
            try {
                if (ProductImage::where('product_id', $id)->where('is_main', true)->count() === 0) {
                    $image = ProductImage::where('product_id', $id)->where('is_main', false)->orderBy('id', 'desc')->first();
                    $image->is_main = true;
                    $image->save();
                }
            } catch (\Exception $e) {
            }
        }

        try {
            Artisan::call('cache:clear');
            Product::flushCache();
        } catch (\Exception $e) {
        }

        return $response;
    }

    /**
     * This is used to update pricing and availability_id (mass update)
     * You can use it to update any other column, just Try to make
     * It more readable and more clear
     *
     * @return Response
     */
    public function ajaxUpdate(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $this->authorize('browse', app(Product::class));

        $request->validate([
            'ids' => 'required',
            'column' => ['required', 'in:price,availability_id']
        ]);

        $ids = Arr::wrap(
            explode(',', $request->input('ids')) ?? []
        );

        $query = Product::whereIn('id', $ids);

        if ($request->input('column') === 'availability_id') {
            return response()->json(
                $query->update([
                    'availability_id' => $request->input('availability_id')
                ])
            );
        }

        if ($request->input('markup_type') === 'amount') {
            return response()->json(
                $query->update([
                    'price' => DB::raw('price + ' . floatval($request->input('amount')))
                ])
            );
        }

        if ($request->input('markup_type') === 'markup') {
            return response()->json(
                $query->update([
                    'price' => DB::raw('price * ' . floatval($request->input('markup')))
                ])
            );
        }

        return response()->json(false, 500);
    }

    /**
     * Handle product options CRUD
     *
     * @return Response
     */
    public function updateProductOptions(Request $request, Product $product)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $this->authorize('edit', app(Product::class));

        $dataType = Voyager::model('DataType')
            ->where('slug', $this->getSlug($request))
            ->first();

        $editable = $dataType->editRowsInputs->pluck('field')->toArray();

        $child = $product;

        try {
            try {
                $child = Product::withoutGlobalScopes()->findOrFail($request->input('id'));

                Product::withoutGlobalScopes()->where('id', $child->id)->update([
                    'parent_id' => $product->id
                ]);
            } catch (\Exception $e) {
                $child = Product::create($request->only($editable));

                Product::withoutGlobalScopes()->where('id', $child->id)->update([
                    'item_id' => $child->id,
                    'parent_id' => $product->id
                ]);
            }

            Product::flushCache();
        } catch (\Exception $e) {
        }

        return $child;
    }

    /**
     * Destroy product option
     *
     * @param  Request $request
     * @param  Product $product
     * @return Response
     */
    public function destroyProductOptions(Request $request, Product $product)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $this->authorize('delete', app(Product::class));

        $request->validate([
            'id' => 'required|exists:products,id'
        ]);

        $child = Product::withoutGlobalScopes()->findOrFail($request->input('id'));

        Product::withoutGlobalScopes()->where('id', $request->input('id'))->update([
            'parent_id' => null
        ]);

        try {
            $child->delete();
            Artisan::call('cache:clear');
            Product::flushCache();
        } catch (\Exception $e) {
        }

        return $product;
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function updateCategories(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $product = Product::withoutGlobalScopes()->findOrFail($request->input('id'));
        $product->categories()->detach();

        try {
            $product->categories()->attach($request->input('categories'));
            Artisan::call('cache:clear');
            Product::flushCache();
        } catch (\Exception $e) {
        }

        return $product;
    }

    /**
     * Remove product images
     *
     * @return Response
     */
    public function destroyImage(Product $product, Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $this->authorize('delete', app(Product::class));

        $request->validate([
            'id' => 'required|exists:product_images,id'
        ]);

        $image = ProductImage::withoutGlobalScopes()->findOrFail($request->input('id'));

        // this piece may change
        try {
            Storage::disk('public')->delete($image->src);
            $image->delete();
            return response()->json(true);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * @return string
     */
    public function export()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $fh = fopen('php://output', 'w');

        ob_start();

        fputcsv($fh, [
            'ID',
            'SKU',
            'Brand',
            'Model/MPN',
            'Title',
            'MSRP',
            'MAP',
            'Cost',
            'Selling Price',
            'Image',
            'Main Category',
            'Family',
            'Status',
            'Availability',
            'Home Page Order'
        ]);

        $products = Product::with(['children', 'categories', 'brand', 'parent', 'images', 'availability'])
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->get();

        foreach ($products as $product) {
            fputcsv($fh, [
                $product->id,
                $product->sku,
                $product->brand->name ?? '--',
                $product->mpn,
                $product->name,
                $product->compare_at_price,
                $product->map_price,
                $product->cost,
                $product->price,
                asset($product->main_image),
                $product->categories->pluck('name')->implode(' > '),
                $product->nested_type,
                $product->status_name,
                $product->availability->name ?? '--',
                $product->homepage_order,
            ]);
        }

        $string = ob_get_clean();

        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename=AW-'. date('Y-m-d h:i:sA') .'.csv',
                'Expires'             => '0',
                'Pragma'              => 'public',
        ];

        return response()->stream(function () use ($string) {
            echo $string;
        }, 200, $headers);
    }
}
