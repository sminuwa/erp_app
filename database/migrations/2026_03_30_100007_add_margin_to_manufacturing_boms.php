<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarginToManufacturingBoms extends Migration
{
    public function up()
    {
        Schema::table('manufacturing_boms', function (Blueprint $table) {
            $table->decimal('margin_per_piece', 15, 4)->nullable()->after('total_cost_per_unit');
            $table->unsignedBigInteger('margin_gl_account_id')->nullable()->after('margin_per_piece');
            $table->foreign('margin_gl_account_id')->references('id')->on('general_accounts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('manufacturing_boms', function (Blueprint $table) {
            $table->dropForeign(['margin_gl_account_id']);
            $table->dropColumn(['margin_per_piece', 'margin_gl_account_id']);
        });
    }
}
