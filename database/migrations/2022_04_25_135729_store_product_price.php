<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BranchProductPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_product_prices', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->foreignId('branch_id')->references('id')->on('branches');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->float('selling_price');
            $table->tinyInteger('status')->default(1);
            $table->foreignId('updated_by')->references('id')->on('users');
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
        Schema::dropIfExists('branch_product_prices');
    }
}
