<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class CategoriesBreadTypeAdded extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     *
     * @throws Exception
     */
    public function run()
    {
        try {
            \DB::beginTransaction();

            $dataType = DataType::where('name', 'categories')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'categories')->delete();
            }

            \DB::table('data_types')->insert(array(
                'id' => 10,
                'name' => 'categories',
                'slug' => 'categories',
                'display_name_singular' => 'Category',
                'display_name_plural' => 'Categories',
                'icon' => 'fas fa-bars',
                'model_name' => 'App\\Category',
                'policy_name' => null,
                'controller' => '\\TCG\\Voyager\\Http\\Controllers\\Bread\\CategoryBreadController',
                'nested_controller' => '\\TCG\\Voyager\\Http\\Controllers\\VoyagerNestedController',
                'nested_max_depth' => 3,
                'description' => 'Product categories for your site',
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"sort_order","order_display_column":"name","order_direction":"desc","default_search_key":"name","scope":null}',
                'created_at' => now(),
                'updated_at' => now(),
            ));

            Voyager::model('Permission')->generateFor('categories');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Categories',
                'url' => '',
                'route' => 'voyager.categories.nested.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'fas fa-bars',
                    'color' => null,
                    'parent_id' => null,
                    'order' => $order,
                ])->save();
            }
        } catch (Exception $e) {
            throw new Exception('Exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}
