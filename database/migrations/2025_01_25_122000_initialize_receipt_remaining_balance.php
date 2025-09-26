<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitializeReceiptRemainingBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update existing receipts to set remaining_balance equal to amount
        // Only for posted receipts (status = 1)
        DB::statement('UPDATE receipts SET remaining_balance = amount WHERE status = 1 AND remaining_balance = 0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reset remaining_balance to 0 for all receipts
        DB::statement('UPDATE receipts SET remaining_balance = 0');
    }
}