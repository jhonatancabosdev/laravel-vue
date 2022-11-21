<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class ShippingsBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'shippings')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'shippings')->delete();
            }

            \DB::table('data_types')->insert(array (
                'name' => 'shippings',
                'slug' => 'shippings',
                'display_name_singular' => 'Shipping Option',
                'display_name_plural' => 'Shipping Options',
                'icon' => 'fas fa-truck',
                'model_name' => 'App\\Shipping',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => 'This is where you will manage shipping options for your site',
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"created_at","order_display_column":"created_at","order_direction":"asc","default_search_key":"created_at","scope":null}',
                'created_at' => now(),
                'updated_at' => now(),
            ));

            Voyager::model('Permission')->generateFor('shippings');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Shipping Options',
                'url' => '',
                'route' => 'voyager.shippings.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'fa fa-truck',
                    'color' => null,
                    'parent_id' => null,
                    'order' => $order,
                ])->save();
            }
        } catch(Exception $e) {
           throw new Exception('Exception: ' . $e);

           \DB::rollBack();
        }

        \DB::commit();
    }
}
