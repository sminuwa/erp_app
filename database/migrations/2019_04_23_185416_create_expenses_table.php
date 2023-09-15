<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('expense_item_id')->references('id')->on('expense_items');
            $table->string('name');
            $table->float('amount');
            $table->integer('month')->nullable();
            $table->year('year')->nullable();
            $table->integer('day')->nullable();
            $table->foreignId('payment_mode_id')->references('id')->on('payment_modes');
            $table->string('impress')->nullable();
            $table->foreignId('bank_account_id')->references('id')->on('bank_accounts');
            $table->string('reason')->nullable();
            $table->date('date');
            $table->enum('status',['Completed','Cancelled'])->default('Completed');
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
        Schema::dropIfExists('expenses');
    }
}
