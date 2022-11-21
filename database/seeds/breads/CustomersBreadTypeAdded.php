<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\MenuItem;

class CustomersBreadTypeAdded extends Seeder
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

            $dataType = DataType::where('name', 'customers')->first();

            if (is_bread_translatable($dataType)) {
                $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
            }

            if ($dataType) {
                DataType::where('name', 'customers')->delete();
            }

            \DB::table('data_types')->insert(array (
                'name' => 'customers',
                'slug' => 'customers',
                'display_name_singular' => 'Customer',
                'display_name_plural' => 'Customers',
                'icon' => 'fas fa-user-friends',
                'model_name' => 'App\\Customer',
                'policy_name' => NULL,
                'controller' => NULL,
                'description' => 'This is where you will manage customers who have accounts on your site',
                'generate_permissions' => 1,
                'server_side' => 1,
                'details' => '{"order_column":"created_at","order_display_column":"created_at","order_direction":"asc","default_search_key":"created_at","scope":null}',
                'created_at' => now(),
                'updated_at' => now(),
            ));

            Voyager::model('Permission')->generateFor('customers');

            $menu = Menu::where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'title' => 'Customers',
                'url' => '',
                'route' => 'voyager.customers.index',
            ]);

            $order = Voyager::model('MenuItem')->highestOrderMenuItem();

            if (!$menuItem->exists) {
                $menuItem->fill([
                    'target' => '_self',
                    'icon_class' => 'fas fa-user-friends',
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
