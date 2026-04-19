<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateMaterialsRequisitions extends Migration
{
    public function up()
    {
        Schema::table('materials_requisitions', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_id')->nullable()->after('schedule_id');
            $table->unsignedBigInteger('verified_by')->nullable()->after('received_at');
            $table->timestamp('verified_at')->nullable()->after('verified_by');

            $table->foreign('work_order_id')->references('id')->on('manufacturing_work_orders')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });

        // Data migration: promote all ISSUED requisitions to VERIFIED so they can still be received
        DB::statement("
            UPDATE materials_requisitions
            SET status = 'verified',
                verified_by = issued_by,
                verified_at = issued_at
            WHERE status = 'issued'
        ");
    }

    public function down()
    {
        // Revert verified → issued before dropping columns
        DB::statement("
            UPDATE materials_requisitions
            SET status = 'issued'
            WHERE status = 'verified'
        ");

        Schema::table('materials_requisitions', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['work_order_id', 'verified_by', 'verified_at']);
        });
    }
}
