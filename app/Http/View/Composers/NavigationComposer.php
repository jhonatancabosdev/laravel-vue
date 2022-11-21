<?php 

namespace App\Http\View\Composers;

use App\Category;
use App\Brand;
use Illuminate\Contracts\View\View;

class NavigationComposer
{
    /**
     * @var categories collection
     */
    protected $navigationCategories;
 
    /**
     * @var brands collection
     */
    protected $navigationBrands;
 
    /**
     * @var professions collection
     */
    protected $navigationProfessions;
 
    /**
     * Create a new categories composer.
     *
     * @param  Category $category
     * @param  Brand $category
     * @return void
     */
    public function __construct()
    {
        if (!$this->navigationCategories) {
            $this->navigationCategories = Category::where('parent_id', NULL)->where('on_navbar', 1)->with('children.parent')->orderBy('homepage_order')->get();
        }
        // if (!$this->navigationBrands) {
        //     $this->navigationBrands = Brand::where('status', 1)->get();
        // }
        if (!$this->navigationProfessions) {
            $this->navigationProfessions = collect([]);
        }

    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'navigationCategories'    =>$this->navigationCategories,
            'navigationBrands'    => $this->navigationBrands,
            'navigationProfessions'    => $this->navigationProfessions,
        ]);
    }
}