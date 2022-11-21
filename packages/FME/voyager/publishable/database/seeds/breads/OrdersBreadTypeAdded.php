<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class OrdersBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'orders')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'orders')->delete();
            }

            \DB::table('data_types')->insert(array(
                'id' => 8,
                'name' => 'orders',
                'slug' => 'orders',
                'display_name_singular' => 'Order',
                'display_name_plural' => 'Orders',
                'icon' => 'fas fa-dollar-sign',
                'model_name' => 'App\\Order',
                'policy_name' => null,
                'controller' => '\\TCG\\Voyager\\Http\\Controllers\\Bread\\OrderBreadController',
                'description' => 'This is where you will find the orders your site has received',
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"created_at","order_display_column":"created_at","order_direction":"asc","default_search_key":"created_at","scope":null}',
                'created_at' => now(),
                'updated_at' => now(),
            ));

            Voyager::model('Permission')->generateFor('orders');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Orders',
                'url' => '',
                'route' => 'voyager.orders.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'fas fa-dollar-sign',
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
