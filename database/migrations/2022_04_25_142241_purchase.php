<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Purchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->string('invoice');
            $table->date('purchase_date')->useCurrent();
            $table->enum('purchase_mode',['Cash','Credit','Cash/Credit'])->default('Cash');
            $table->string('address')->nullable();
            $table->string('wbno')->nullable();
            $table->foreignId('source_store_id')->references('id')->on('stores');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->integer('qty_supplied')->default(0);
            $table->decimal('unit_price',18,2)->default(0);
            $table->foreignId('destination_store_id')->references('id')->on('stores');
            $table->tinyInteger('status')->default(1);
            $table->foreignId('updated_by')->references('id')->on('users');
            $table->timestamps();
            //$table->index(['invoice','purchase_date','purchase_mode','supplier_id','wbno','source_store_id','product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
