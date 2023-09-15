<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BankAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts',function(BluePrint $table){
            $table->bigIncrements('id');
            $table->string('account_name',100)->unique();
            $table->string('account_no')->nullable();
            $table->foreignId('bank_branch_id')->references('id')->on('bank_branches');
            $table->decimal('account_balance',18,2)->default(0);
            $table->enum('account_type',['Current','Savings','Credit','Domiciliary','Cash']);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('bank_accounts');
    }
}
