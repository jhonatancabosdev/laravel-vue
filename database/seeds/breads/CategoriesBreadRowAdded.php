<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class CategoriesBreadRowAdded extends Seeder
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

            \DB::table('data_rows')->insert(array(
                0 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'id',
                    'type' => 'text',
                    'display_name' => 'Id',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => json_encode([]),
                    'order' => 1,
                ),
                1 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'name',
                    'type' => 'text',
                    'display_name' => 'Name',
                    'required' => 1,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 2,
                ),
                2 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'slug',
                    'type' => 'text',
                    'display_name' => 'Slug',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 3,
                ),
                3 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'cover',
                    'type' => 'image',
                    'display_name' => 'Cover',
                    'required' => 0,
                    'browse' => 1,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 4,
                ),
                4 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'description',
                    'type' => 'text',
                    'display_name' => 'Description',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 5,
                ),
                5 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'marketing_description',
                    'type' => 'text',
                    'display_name' => 'Marketing Description',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 6,
                ),
                6 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'status',
                    'type' => 'checkbox',
                    'display_name' => 'Status',
                    'required' => 1,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 0,
                    'delete' => 0,
                    'details' => '{"on":"Enabled","off":"Disabled"}',
                    'order' => 7,
                ),
                7 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'on_navbar',
                    'type' => 'checkbox',
                    'display_name' => 'On Navbar',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '{"on":"Yes","off":"No"}',
                    'order' => 8,
                ),
                8 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'on_filter',
                    'type' => 'checkbox',
                    'display_name' => 'On Filter',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => '{"on":"Yes","off":"No"}',
                    'order' => 9,
                ),
                9 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'homepage_order',
                    'type' => 'text',
                    'display_name' => 'Index order',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 10,
                ),
                10 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => '_lft',
                    'type' => 'text',
                    'display_name' => 'Lft',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 11,
                ),
                11 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => '_rgt',
                    'type' => 'text',
                    'display_name' => 'Rgt',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 12,
                ),
                12 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'parent_id',
                    'type' => 'text',
                    'display_name' => 'Parent Id',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => json_encode([]),
                    'order' => 13,
                ),
                13 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'created_at',
                    'type' => 'timestamp',
                    'display_name' => 'Created At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => json_encode([]),
                    'order' => 14,
                ),
                14 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'display_order',
                    'type' => 'text',
                    'display_name' => 'Display Order',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 1,
                    'add' => 1,
                    'delete' => 1,
                    'details' => json_encode([]),
                    'order' => 14,
                ),
                15 =>
                array(
                    'data_type_id' => $dataType->id,
                    'field' => 'updated_at',
                    'type' => 'timestamp',
                    'display_name' => 'Updated At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 0,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => json_encode([]),
                    'order' => 15,
                ),
            ));
        } catch (Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}
