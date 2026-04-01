<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufacturingWorkOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('manufacturing_work_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('schedule_item_id');
            $table->decimal('planned_qty', 15, 4);
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('manufacturing_work_orders')->onDelete('cascade');
            $table->foreign('schedule_item_id')->references('id')->on('daily_manufacturing_schedule_items')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('manufacturing_work_order_items');
    }
}
