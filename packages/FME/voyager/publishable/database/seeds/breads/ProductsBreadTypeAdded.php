<?php

use TCG\Voyager\Models\Menu;
use Illuminate\Database\Seeder;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\MenuItem;

class ProductsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'products')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'products')->delete();
            }

            \DB::table('data_types')->insert([
                'name' => 'products',
                'slug' => 'products',
                'display_name_singular' => 'Product',
                'display_name_plural' => 'Products',
                'icon' => 'fas fa-store',
                'model_name' => 'App\\Product',
                'policy_name' => null,
                'controller' => '\\TCG\\Voyager\\Http\\Controllers\\Bread\\ProductBreadController',
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"name","order_display_column":"name","order_direction":"desc","default_search_key":"name","scope":null}',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Voyager::model('Permission')->generateFor('products');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))
                ->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Products',
                'url' => '',
                'route' => 'voyager.products.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'fas fa-store',
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
