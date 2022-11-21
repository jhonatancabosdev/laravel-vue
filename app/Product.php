<?php

namespace App;

use \Spatie\Tags\HasTags;
use Illuminate\Support\Str;
use App\Scopes\EnabledScope;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\{Model, Order, Brand, Country, Customer, Category, Manufacture, ProductImage, Availability, Review};
use Spatie\Tags\Tag;

class Product extends Model
{
    use HasTags, NodeTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'main_image', 'small_main_image', 'large_main_image', 'original_price'
    ];

    /**
     * @var array
     */
    protected $with = ['images', 'tags'];

    /**
     * Searchable columns
     *
     * @var array
     */
    protected $searchableColumns = [
        'id', 'upc', 'sku', 'mpn', 'name', 'description'
    ];

    /**
     * @var array
     */
    protected static $freeShippingOptions = [
        "Not free shipping",
        "Free Ground",
        "Free 2-day"
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new EnabledScope);
    }


    public function getAllTagTypesAttribute()
    {
        return Tag::groupBy('type')->get()->map(function($item){
            $item->title = $this->tags->firstWhere('type', $item->type)->name ?? '';
            return $item;
        })->filter(function($item){
            return !empty($item->type);
        })->sortByDesc('title');
    }

    /**
     * Get MAP enabled
     *
     * @return float
     */
    public function getIsMapEnabledAttribute()
    {
        try {
            if (isset($this->attributes['is_map_enabled'])) {
                return (int) $this->attributes['is_map_enabled'];
            }

            if ($this->exists && isset($this->is_map_enabled)) {
                return (int) $this->is_map_enabled;
            }
        } catch (\Exception $e) {
        }

        return 0;
    }

    /**
     * @param mixed
     */
    public function setIsMapEnabledAttribute($value)
    {
        switch (trim(strtolower($value))) {
            case 'on':
                $this->attributes['is_map_enabled'] = 1;
                break;

            case 'off':
                $this->attributes['is_map_enabled'] = 0;
                break;

            default:
                $this->attributes['is_map_enabled'] = is_null($value) ? 0 : $value;
                break;
        }
    }

    /**
     * @param mixed
     */
    public function setIsOnFeedAttribute($value)
    {
        switch (trim(strtolower($value))) {
            case 'on':
                $this->attributes['is_on_feed'] = 1;
                break;

            case 'off':
                $this->attributes['is_on_feed'] = 0;
                break;

            default:
                $this->attributes['is_on_feed'] = is_null($value) ? 0 : $value;
                break;
        }
    }

    /**
     * Get main image
     *
     * @return string
     */
    public function getSmallMainImageAttribute()
    {
        $image = $this->images->where('is_main', true)->first();

        $imageUrl = (is_null($image) || !Storage::disk('public')->exists($image->src))
                        ? '/storage/' . config('default-variables.default-image')
                        : '/storage/' . $image->src;
        return \Bkwld\Croppa\Facade::url($imageUrl, 300, 300, array('pad'));
    }

    public function getLargeMainImageAttribute()
    {
        $image = $this->images->where('is_main', true)->first();

        $imageUrl = (is_null($image) || !Storage::disk('public')->exists($image->src))
                        ? '/storage/' . config('default-variables.default-image')
                        : '/storage/' . $image->src;

        return $imageUrl;
    }

    /**
     * @return array
     */
    public static function getFreeShippingOptions()
    {
        return self::$freeShippingOptions;
    }

    public function getMainImageAttribute()
    {
        $imageUrl = '/storage/products/products_' . $this->slug . '_white_1000.jpg';
        if(!file_exists(public_path($imageUrl))) {
            $imageUrl = '/storage/products/products_' . $this->slug . '_white.jpg';
            if(!file_exists(public_path($imageUrl))) {
                $imageUrl = '/storage/products/products_' . $this->slug . '_gray.jpg';
                if(!file_exists(public_path($imageUrl))) {
                    $imageUrl = '/storage/products/' . $this->slug . '.jpg';
                    if(!file_exists(public_path($imageUrl))) {
                        $image = $this->images->where('is_main', true)->first();

                        $imageUrl = (is_null($image) || !Storage::disk('public')->exists($image->src))
                                    ? '/storage/' . config('default-variables.default-image')
                                    : '/storage/' . $image->src;
                    }
                }
            }
        }
        return $imageUrl;
    }

    /**
     * Get condition attribute
     *
     * @return string
     */
    public function getConditionAttribute()
    {
        return ucfirst($this->attributes['condition'] ?? 'new');
    }

    /**
     * Get original price - USED ON SOME SITES
     *
     * @return float
     */
    public function getOriginalPriceAttribute()
    {
        return $this->price * 1.24;
    }

    /**
     * Set slug attribute
     *
     * @param string $value
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Set status
     *
     * @param string $value
     */
    public function setStatusAttribute($value)
    {
        if (isset($value) && in_array(strtolower($value), ['on', 'off'])) {
            $value = $value == 'on' ? 1 : 0;
        }

        $this->attributes['status'] = $value;
    }

    /**
     * @param mixed
     */
    public function setCostAttribute($value)
    {
        try {
            $value = preg_replace('/[^0-9.]/', '', $value);
            $this->attributes['cost'] = round(floatval($value), 2);
        } catch (\Exception $e) {

        }
    }

    /**
     * @param mixed
     */
    public function getSellingPriceAttribute($value)
    {
        try {
            $value = preg_replace('/[^0-9.]/', '', $value);
            $this->attributes['price'] = round(floatval($value), 2);
        } catch (\Exception $e) {

        }
    }
    /**
     * @param mixed
     */
    public function setPriceAttribute($value)
    {
        try {
            $value = preg_replace('/[^0-9.]/', '', $value);
            $this->attributes['price'] = round(floatval($value), 2);
        } catch (\Exception $e) {

        }
    }

    /**
     * @param mixed
     */
    public function setOriginalPriceAttribute($value)
    {
        try {
            $value = preg_replace('/[^0-9.]/', '', $value);
            $this->attributes['original_price'] = round(floatval($value), 2);
        } catch (\Exception $e) {

        }
    }

    /**
     * @param mixed
     */
    public function setMapPriceAttribute($value)
    {
        try {
            $value = preg_replace('/[^0-9.]/', '', $value);
            $this->attributes['map_price'] = round(floatval($value), 2);
        } catch (\Exception $e) {

        }
    }

    /**
     * Product availability
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }

    /**
     * Product brand
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


    /**
     * Product options
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function options()
    {
        return $this->belongsToMany(Option::class);
    }

    /**
     * Product option values
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function optionValues()
    {
        return $this->belongsToMany(OptionValue::class);
    }

    /**
     * Product manufacture
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manufacture()
    {
        return $this->belongsTo(Manufacture::class);
    }

    /**
     * Product country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Children's products
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id', 'id');
    }

    /**
     * Parent product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id', 'id');
    }

    /**
     * Product images
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Product reviews
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Product orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class)
                    ->withPivot(['quantity', 'options', 'price']);
    }

    /**
     * Product categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Product categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getCategoryAttribute()
    {
        try {
            return $this->categories()->orderBy('id', 'desc')->remember(self::getDefaultCacheTime())->get()->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Product parent categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function getParentCategoriesAttribute()
    {
        if ($this->category instanceof Category) {
            return Category::remember(config('default-variables.cache_life_time'))
                ->ancestorsAndSelf($this->category);
        }

        return collect();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $searchTerm
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $searchTerm)
    {
        $terms = explode(
            ' ',
            str_replace(
                ['-', '_', '$', 'ˆ', '"', '\''],
                ' ',
                mb_strtolower(
                    trim($searchTerm)
                )
            )
        );

        $categories = Category::query();
        foreach ($terms as $term) {
            $categories->orWhere('slug', 'like', '%' . $term . '%');
        }
        $categories = $categories->get();

        $query = $query->where('products.status',true)
            ->where(function($query) use ($searchTerm){

                foreach ($this->searchableColumns as $column) {
                    $query = $query->orWhereRaw('LOWER(products.'.$column.') LIKE ? ', ['%'.trim(strtolower($searchTerm)).'%']);
                }

                $string = str_replace(['-', '_', '$', 'ˆ', '"', '\''], ' ', $searchTerm);
                $words = explode(' ', $string);

                $searchTerm = preg_replace('/[^a-zA-Z0-9\d]/', '', $searchTerm);

                /* foreach (['name', 'short_description', 'description'] as $column) {
                    $query = $query->orWhereRaw("REGEXP_REPLACE(LOWER(REGEXP_REPLACE (".$column.", '<.+?>', '')), '[^a-zA-Z0-9\]', '') LIKE ?", ['%'.trim(strtolower($searchTerm)).'%']);
                }

                foreach ($words as $key => $value) {
                    if ((strlen($value) < 3 || is_numeric($value)) && isset($words[$key + 1])) {
                        $words[$key + 1] = $value . $words[$key + 1];
                        continue;
                    }

                    $query = $query->orWhereRaw("REGEXP_REPLACE(LOWER(REGEXP_REPLACE (description, '<.+?>', '')), '[^a-zA-Z0-9\]', '') LIKE ?", ['%'.trim(strtolower($value)).'%']);
                } */                
            })
            ->orWhere(function($query) use ($searchTerm){
                foreach (['name'] as $column) {

                    $searchTerm = preg_replace('/[^a-zA-Z0-9\d]/', '', $searchTerm);

                    $fuzzySearch = implode("%", array_filter(str_split($searchTerm), function ($term) {
                        return trim($term) !== '';
                    }));

                    $fuzzySearch = "%$fuzzySearch%"; // test -> %t%e%s%t%s%

                    $query = $query->orWhereRaw('LOWER(products.'.$column.') LIKE ? ', ['%'.trim(strtolower($fuzzySearch)).'%']);
                }
            });

        if ($categories->count() > 0) {
            $query = $query->leftJoin('category_product', 'id', 'product_id');
            $query = $query->orWhereIn('category_product.category_id', $categories->map(function($c) { return $c->id; }));
            $query = $query->orderByRaw("CASE WHEN category_product.category_id in (". $categories->implode('id', ",") . ") THEN 0 ELSE 1 END");
        }

        if (count($terms) > 0) {
            foreach ($terms as $term) {
                $query = $query->orderByRaw("CASE WHEN instr(LOWER(products.name), '$term') THEN 0 ELSE 1 END");
            }
        }
        //Show Xerox products first
        return $query->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->orderByRaw('CASE WHEN brands.slug = \'xerox\' THEN 0 ELSE 1 END')
            ->orderByRaw("CHAR_LENGTH(products.name) asc", [trim(strtolower($searchTerm)), trim(strtolower($searchTerm))])
            ->orderBy('products.name', 'asc');
    }

    public function scopeTopLevel($query)
    {
        return $query->where('parent_id', NULL);
    }

}
