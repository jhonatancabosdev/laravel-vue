<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNestedFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
                        // product parent/childs
                        $table->unsignedBigInteger('_lft')->default(0);
                        $table->unsignedBigInteger('_rgt')->default(0);
                        $table->index(['_lft', '_rgt', 'parent_id']);            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['_lft', '_rgt', 'parent_id']);
            $table->dropColumn(['_lft','_rgt']);
        });
    }
}
