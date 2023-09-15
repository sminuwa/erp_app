<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierLedger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_ledgers', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->string('description');
            $table->string('Ref');
            $table->decimal('cr',18,2)->default(0);
            $table->decimal('dr',18,2)->default(0);
            $table->date('date')->useCurrent();
            $table->timestamps();
            $table->index(['supplier_id','Ref','cr','dr','date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_ledgers');
    }
}
