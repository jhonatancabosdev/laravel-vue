<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('name')->nullable();
            $table->text('description')->nullable();
            $table->string('coupon_code', 255);
            $table->string('discount_type', 255);// ex. reduction of total, free shipping, reduction on specific product, etc.
            $table->decimal('discount_amount', 8, 2);
            $table->string('discount_method', 25);// ex. Percentage off or dollars off
            $table->dateTime('expiration_date')->default(\Carbon\Carbon::now());
            $table->dateTime('activation_date')->default(\Carbon\Carbon::now());
            $table->boolean('is_active')->default(FALSE);
            $table->boolean('is_triggerable')->default(FALSE);
            $table->decimal('trigger_amount')->nullable();
            $table->boolean('collects_email')->default(FALSE);
            $table->integer('courier_id')->unsigned()->nullable();
            $table->softDeletes();
        });
        Schema::create('discount_product', function (Blueprint $table) {
            $table->primary(['product_id', 'discount_id']);
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('discount_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('category_discount', function (Blueprint $table) {
            $table->primary(['category_id', 'discount_id']);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('discount_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('cascade');
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
        Schema::table('category_discount', function (Blueprint $table) {
            $table->dropForeign('category_discount_discount_id_foreign');
            $table->dropForeign('category_discount_category_id_foreign');
        });
        Schema::table('discount_product', function (Blueprint $table) {
            $table->dropForeign('discount_product_discount_id_foreign');
            $table->dropForeign('discount_product_product_id_foreign');
        });
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('discount_product');
        Schema::dropIfExists('category_discount');
    }
}
