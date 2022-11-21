<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class BrandsBreadRowAdded extends Seeder
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

            $dataType = DataType::where('name', 'brands')->first();

            $data = [];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'id',
                'type' => 'text',
                'display_name' => 'Id',
                'required' => 0,
                'browse' => 1,
                'read' => 0,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'name',
                'type' => 'text',
                'display_name' => 'Name',
                'required' => 1,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 0,
                'details' => '{"slugify":{"origin":"name","forceUpdate":true}}',
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'slug',
                'type' => 'text',
                'display_name' => 'Slug',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => '{"slugify":{"origin":"name","forceUpdate":true}}',
            ];

            $data[] = [
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
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'description',
                'type' => 'text',
                'display_name' => 'Description',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'status',
                'type' => 'radio_btn',
                'display_name' => 'Status',
                'required' => 0,
                'browse' => 0,
                'read' => 0,
                'edit' => 0,
                'add' => 0,
                'delete' => 1,
                'details' => '{ "default" : "1", "options" : { "0": "Disabled", "1": "Enabled" } }',
            ];
            
            \DB::table('data_rows')->insert(
                collect($data)->transform(function ($e, $index) {
                    $e['order'] = $index;
                    return $e;
                })->toArray()
            );
        } catch (Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}
