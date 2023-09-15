<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_limits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('customer_id')->references('id')->on('customers');
            $table->decimal('amount',18,2)->default(0);
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
        //
    }
}
