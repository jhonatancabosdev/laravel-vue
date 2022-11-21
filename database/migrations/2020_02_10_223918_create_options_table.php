<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->string('slug');
        });

        Schema::create('option_product', function (Blueprint $table) {
            $table->unsignedBigInteger('option_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->foreign('option_id')->references('id')->on('options');
            $table->foreign('product_id')->references('id')->on('products');
            $table->primary(['option_id','product_id']);
        });

        Schema::create('option_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('option_id')->index();
            $table->foreign('option_id')->references('id')->on('options');
            $table->string('name');
            $table->string('slug');
        });

        Schema::create('option_value_product', function (Blueprint $table) {
            $table->unsignedBigInteger('option_value_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->foreign('option_value_id')->references('id')->on('option_values');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('option_value_product');
        Schema::dropIfExists('option_values');
        Schema::dropIfExists('option_product');
        Schema::dropIfExists('options');
    }
}
