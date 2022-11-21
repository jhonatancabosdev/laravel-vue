<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->string('cc_number')->nullable();
            $table->string('cc_name')->nullable();
            $table->string('cc_expiration')->nullable();
            $table->string('cc_expiration_month')->nullable();
            $table->string('cc_expiration_year')->nullable();
            $table->string('cc_cvv')->nullable();
            $table->string('card_type')->nullable();

            $table->text('gclid')->nullable();

            $table->decimal('tax_rate', 12, 6)->default(0.00);
            $table->decimal('tax', 12, 6)->default(0.00);
            $table->decimal('shipping_cost', 12, 6)->default(0.00);
            $table->decimal('subtotal', 12, 6)->default(0.00);
            $table->decimal('total', 12, 6)->default(0.00);

            $table->unsignedInteger('order_status_id')->nullable();
            $table->unsignedInteger('shipping_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->boolean('confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();

            $table->boolean('mailed')->nullable();
            $table->timestamp('mailed_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('shipping_id')->references('id')->on('shippings');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
        });

        Schema::table('addresses', function(Blueprint $table){
            $table->unsignedBigInteger('order_id')->nullable()->after('customer_id');
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function(Blueprint $table){
            $table->dropForeign('addresses_order_id_foreign');
            $table->dropColumn('order_id');
        });

        Schema::dropIfExists('orders');
    }
}
