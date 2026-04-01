<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionOrderChecklistsTable extends Migration
{
    public function up()
    {
        Schema::create('production_order_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->string('category'); // machines, staffs, raw_materials, factory_condition
            $table->string('label');
            $table->boolean('is_checked')->default(false);
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->foreign('production_order_id')->references('id')->on('production_orders')->onDelete('cascade');
            $table->foreign('checked_by')->references('id')->on('users')->onDelete('set null');
        });

        // Auto-create checklist rows for all existing production orders that are still pending
        // Mark them as already checked so they are not blocked by the new requirement
        $orders = \DB::table('production_orders')->get();
        foreach ($orders as $order) {
            $items = [
                ['category' => 'machines',          'label' => 'Machines are available and operational'],
                ['category' => 'staffs',             'label' => 'Required staff are assigned and present'],
                ['category' => 'raw_materials',      'label' => 'Raw materials are in stock and ready'],
                ['category' => 'factory_condition',  'label' => 'Factory conditions are safe and ready'],
            ];
            foreach ($items as $item) {
                \DB::table('production_order_checklists')->insert([
                    'production_order_id' => $order->id,
                    'category'            => $item['category'],
                    'label'               => $item['label'],
                    // Pre-check for existing orders so they are not blocked
                    'is_checked'          => true,
                    'checked_by'          => $order->created_by,
                    'checked_at'          => now(),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('production_order_checklists');
    }
}
