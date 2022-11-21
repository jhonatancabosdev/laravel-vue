<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\DataType;

class ProductsBreadRowAdded extends Seeder
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
                'field' => 'sku',
                'type' => 'text',
                'display_name' => 'Sku',
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
                'field' => 'brand_id',
                'type' => 'text',
                'display_name' => 'brand_id',
                'required' => 0,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'product_belongsto_brand_relationship',
                'type' => 'relationship',
                'display_name' => 'brand',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([
                    "model" => "\\App\\Brand",
                    "table" => "brands",
                    "type" => "belongsTo",
                    "column" => "brand_id",
                    "key" => "id",
                    "label" => "name",
                    "pivot_table" => "addresses",
                    "pivot" => "0",
                    "taggable" => "on"
                ]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'mpn',
                'type' => 'text',
                'display_name' => 'Model/MPN',
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
                'field' => 'name',
                'type' => 'text',
                'display_name' => 'Title',
                'required' => 1,
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
                'type' => 'rich_text_box',
                'display_name' => 'Description',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'map_price',
                'type' => 'text',
                'display_name' => 'MAP',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'compare_at_price',
                'type' => 'text',
                'display_name' => 'MSRP',
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
                'field' => 'free_shipping_option',
                'type' => 'text',
                'display_name' => 'Free Shipping',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'price',
                'type' => 'text',
                'display_name' => 'Price',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([
                    'display' => [
                        'class' => 'jq-money-formatter',
                    ]
                ]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'cost',
                'type' => 'text',
                'display_name' => 'Cost',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];
            
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'width',
                'type' => 'text',
                'display_name' => 'Width',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];
  
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'length',
                'type' => 'text',
                'display_name' => 'Length',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];
            
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'height',
                'type' => 'text',
                'display_name' => 'Height',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'weight',
                'type' => 'text',
                'display_name' => 'Weight',
                'required' => 1,
                'browse' => 0,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'availability_id',
                'type' => 'text',
                'display_name' => 'Availability id',
                'required' => 0,
                'browse' => 0,
                'read' => 0,
                'edit' => 1,
                'add' => 0,
                'delete' => 1,
                'details' => json_encode([
                    'display' => [
                        'class' => 'jq-money-formatter',
                    ]
                ]),
            ];

            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'product_hasmany_product_image_relationship',
                'type' => 'relationship',
                'display_name' => 'Image',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([
                    'model' => '\\App\\ProductImage',
                    'table' => 'product_images',
                    'type' => 'hasMany',
                    'column' => 'product_id',
                    'key' => 'id',
                    'label' => 'src',
                    'pivot_table' => 'addresses',
                    'pivot' => '0',
                    'taggable' =>null
                ]),
            ];
            
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'product_belongstomany_category_relationship',
                'type' => 'relationship',
                'display_name' => 'Main category',
                'required' => 0,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([
                    "model" => "\\App\\Category",
                    "table" => "categories",
                    "type" => "belongsToMany",
                    "column" => "id",
                    "key" => "id",
                    "label" => "name",
                    "pivot_table" => "category_product",
                    "pivot" => "1",
                    "taggable" => "on"
                ]),
            ];
                
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'children',
                'type' => 'relationship',
                'display_name' => 'Family',
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
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => '{ "default" : "1", "options" : { "0": "Disabled", "1": "Enabled", "2": "Draft" } }',
            ];
            
            $data[] = [
                'data_type_id' => $dataType->id,
                'field' => 'product_belongsto_availability_relationship',
                'type' => 'relationship',
                'display_name' => 'Availability',
                'required' => 1,
                'browse' => 1,
                'read' => 1,
                'edit' => 1,
                'add' => 1,
                'delete' => 1,
                'details' => json_encode([
                    "model" => "\\App\\Availability",
                    "table" => "availabilities",
                    "type" => "belongsTo",
                    "column" => "availability_id",
                    "key" => "id",
                    "label" => "name",
                    "pivot_table" => "addresses",
                    "pivot" => "0",
                    "taggable" => null
                ])
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
