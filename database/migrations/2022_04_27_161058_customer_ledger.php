<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomerLedger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_ledgers', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->foreignId('customer_id')->references('id')->on('customers');
            $table->string('systemid');
            $table->string('description');
            $table->string('Ref');
            $table->decimal('cr',18,2)->default(0);
            $table->decimal('dr',18,2)->default(0);
            $table->date('date')->useCurrent();
            $table->timestamps();
            //$table->index(['customer_id','Ref','cr','dr','date','systemid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_ledgers');
    }
}
