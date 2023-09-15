<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransferProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('source_store_id')->references('id')->on('stores');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('destination_store_id')->references('id')->on('stores');
            $table->integer('qty_transfered')->default(0);
            $table->integer('qty_available')->default(0);
            $table->foreignId('transfered_by')->references('id')->on('users');
            $table->enum('status', ['Completed', 'Cancelled'])->default('Completed');
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
        Schema::dropIfExists('transfer_products');
    }
}
