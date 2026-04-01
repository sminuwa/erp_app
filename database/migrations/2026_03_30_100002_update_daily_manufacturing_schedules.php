<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDailyManufacturingSchedules extends Migration
{
    public function up()
    {
        Schema::table('daily_manufacturing_schedules', function (Blueprint $table) {
            // Drop team and machine (moved to work orders)
            $table->dropForeign(['team_id']);
            $table->dropForeign(['machine_id']);
            $table->dropColumn(['team_id', 'machine_id']);

            // Add receive tracking
            $table->unsignedBigInteger('received_by')->nullable()->after('approved_at');
            $table->timestamp('received_at')->nullable()->after('received_by');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('daily_manufacturing_schedules', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['received_by', 'received_at']);

            $table->unsignedBigInteger('team_id')->nullable()->after('production_order_id');
            $table->unsignedBigInteger('machine_id')->nullable()->after('team_id');
            $table->foreign('team_id')->references('id')->on('manufacturing_teams')->onDelete('set null');
            $table->foreign('machine_id')->references('id')->on('manufacturing_machines')->onDelete('set null');
        });
    }
}
