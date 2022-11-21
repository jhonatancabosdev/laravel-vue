<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('processed')->default(false);
            $table->integer('total_rows')->default(0);
            $table->integer('current_row')->default(0);
            $table->longText('file_errors')->nullable();
            $table->string('file_type')->default('boh');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->on('users')->references('id');
        });

        Schema::create('tracking_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('number')->index()->nullable();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_file_id')->nullable();
            $table->string('quantity')->nullable();
            $table->text('lot_number')->nullable();
            $table->unsignedInteger('api_receipt_id')->nullable();
            $table->timestamp('api_receipt_created_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('user_file_id')->references('id')->on('user_files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracking_numbers');
        Schema::dropIfExists('user_files');
    }
}
