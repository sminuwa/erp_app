<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CashMovement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_movements',function(BluePrint $table){
            $table->bigIncrements('id');
            $table->decimal('amount',18,2);
            $table->foreignId('source_account_id')->references('id')->on('bank_accounts');
            $table->foreignId('destination_account_id')->references('id')->on('bank_accounts');
            $table->string('month');
            $table->string('year');
            $table->string('date');
            $table->foreignId('withdraw_by')->references('id')->on('users');
            $table->foreignId('deposited_by')->references('id')->on('users');
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
        Schema::dropIfExists('cash_movements');
    }
}
