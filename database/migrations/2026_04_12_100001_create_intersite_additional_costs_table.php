<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('intersite_additional_costs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference', 30)->unique();
            $table->unsignedBigInteger('intersite_transfer_id');
            $table->unsignedBigInteger('supplier_id');
            $table->enum('cost_mode', ['by_amount', 'by_qty'])->default('by_amount');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('truck_number', 50)->nullable();
            $table->string('waybill_no', 50)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->string('status', 20)->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->foreign('intersite_transfer_id')->references('id')->on('intersite_transfers');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('posted_by')->references('id')->on('users');

            $table->index('intersite_transfer_id');
            $table->index('supplier_id');
            $table->index('branch_id');
            $table->index('status');
        });

        Schema::create('intersite_additional_cost_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('intersite_additional_cost_id');
            $table->unsignedBigInteger('intersite_transfer_product_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 20, 6);
            $table->decimal('original_cost', 15, 2);
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('additional_unit_cost', 15, 4);
            $table->timestamps();

            $table->foreign('intersite_additional_cost_id', 'iac_items_cost_id_foreign')
                ->references('id')->on('intersite_additional_costs')->onDelete('cascade');
            $table->foreign('intersite_transfer_product_id', 'iac_items_transfer_product_id_foreign')
                ->references('id')->on('intersite_transfer_products');
            $table->foreign('product_id')->references('id')->on('products');

            $table->index('intersite_additional_cost_id', 'iac_items_cost_id_index');
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('intersite_additional_cost_items');
        Schema::dropIfExists('intersite_additional_costs');
    }
};
