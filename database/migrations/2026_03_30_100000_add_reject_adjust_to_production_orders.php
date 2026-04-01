<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectAdjustToProductionOrders extends Migration
{
    public function up()
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('rejected_by')->nullable()->after('closed_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            $table->unsignedBigInteger('adjusted_by')->nullable()->after('rejection_reason');
            $table->timestamp('adjusted_at')->nullable()->after('adjusted_by');
            $table->text('adjustment_reason')->nullable()->after('adjusted_at');

            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('adjusted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['adjusted_by']);
            $table->dropColumn([
                'rejected_by', 'rejected_at', 'rejection_reason',
                'adjusted_by', 'adjusted_at', 'adjustment_reason',
            ]);
        });
    }
}
