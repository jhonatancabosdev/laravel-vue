<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            
            $table->bigIncrements('id');

            $table->string('name')->nullable();
            $table->string('slug')->index();

            $table->string('sku')->index()->nullable();
            $table->string('upc')->index()->nullable();
            

            $table->decimal('cost', 12, 6)->default(0);
            $table->decimal('price', 12, 6)->default(0);

            $table->boolean('status')->default(true);
            $table->integer('quantity')->default(0);
            $table->string('quantity_per_case')->nullable();

            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('item_features')->nullable();

            $table->string('weight_uom')->default('lbs')->nullable();
            $table->string('length_uom')->default('in')->nullable();
            $table->string('height_uom')->default('in')->nullable();
            $table->string('width_uom')->default('in')->nullable();

            $table->decimal('weight', 10, 2)->default(0)->nullable();
            $table->decimal('width', 10, 2)->default(0)->nullable();
            $table->decimal('height', 10, 2)->default(0)->nullable();
            $table->decimal('length', 10, 2)->default(0)->nullable();

            $table->unsignedBigInteger('clicks_counter')->default(0);
            $table->unsignedBigInteger('sales_count')->default(0);

            // product parent/childs
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
