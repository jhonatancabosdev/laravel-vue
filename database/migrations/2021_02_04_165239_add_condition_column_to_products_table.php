<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Product;

class AddConditionColumnToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('condition', ['new', 'used', 'remanufactured'])
                  ->after('google_product_category')
                  ->default('new');
        });

        try {
            Product::whereHas('categories', function($query) {
                return $query->whereIn('slug', ['copiers', 'printers', 'scanners']);
            })->each(function ($product) {
                $product->condition = 'remanufactured';
                $product->save();
                $product->attachTag($product->condition, 'Condition');
            });

            Product::whereHas('categories', function($query) {
                return $query->whereNotIn('slug', ['copiers', 'printers', 'scanners']);
            })->each(function ($product) {
                $product->attachTag($product->condition, 'Condition');
            });

        } catch(\Exception $e) {
            logger($e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }
}
