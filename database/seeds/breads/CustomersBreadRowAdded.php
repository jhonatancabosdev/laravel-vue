<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class CustomersBreadRowAdded extends Seeder
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

            $data = [];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'name',
                'type' => 'text',
                'display_name' => 'Name',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 0,
                'details' => json_encode([]),
            ];
            
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'email',
                'type' => 'text',
                'display_name' => 'Email',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 0,
                'details' => json_encode([]),
            ];
            
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'id',
                'type' => 'integer',
                'display_name' => 'id',
                'required' => 0,
                'browse' => 0,
                'read' => 0,
                'edit' => 0,
                'add' => 0,
                'delete' => 0,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'vendor_status',
                'type' => 'number',
                'display_name' => 'Is Vendor',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 0,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'phone',
                'type' => 'text',
                'display_name' => 'Phone',
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

            $data[] = [
                    'data_type_id' => $dataType->id,
                    'field' => 'created_at',
                    'type' => 'timestamp',
                    'display_name' => 'Created At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => json_encode([]),
                    'order' => 14,
            ];

            $data[] = [
                    'data_type_id' => $dataType->id,
                    'field' => 'updated_at',
                    'type' => 'timestamp',
                    'display_name' => 'Updated At',
                    'required' => 0,
                    'browse' => 0,
                    'read' => 1,
                    'edit' => 0,
                    'add' => 0,
                    'delete' => 0,
                    'details' => json_encode([]),
                    'order' => 15,
            ];
            
            \DB::table('data_rows')->insert(
                collect($data)->transform(function($e, $index) {
                    $e['order'] = $index;
                    return $e;
                })->toArray()
            );

        } catch(Exception $e) {
            throw new Exception('exception occur ' . $e);

            \DB::rollBack();
        }

        \DB::commit();
    }
}

