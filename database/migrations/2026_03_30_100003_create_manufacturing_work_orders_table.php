<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufacturingWorkOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('manufacturing_work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('work_order_date');
            $table->unsignedBigInteger('daily_schedule_id');
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->string('status')->default('pending'); // pending, posted
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('daily_schedule_id')->references('id')->on('daily_manufacturing_schedules')->onDelete('restrict');
            $table->foreign('machine_id')->references('id')->on('manufacturing_machines')->onDelete('set null');
            $table->foreign('team_id')->references('id')->on('manufacturing_teams')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('manufacturing_work_orders');
    }
}
