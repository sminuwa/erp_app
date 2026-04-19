<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQcVerifiedToManufacturingTables extends Migration
{
    public function up()
    {
        Schema::table('single_product_manufacturing', function (Blueprint $table) {
            $table->unsignedBigInteger('qc_verified_by')->nullable()->after('status');
            $table->timestamp('qc_verified_at')->nullable()->after('qc_verified_by');
            $table->foreign('qc_verified_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('batch_productions', function (Blueprint $table) {
            $table->unsignedBigInteger('qc_verified_by')->nullable()->after('status');
            $table->timestamp('qc_verified_at')->nullable()->after('qc_verified_by');
            $table->foreign('qc_verified_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('manufacturing_reworks', function (Blueprint $table) {
            $table->unsignedBigInteger('qc_verified_by')->nullable()->after('status');
            $table->timestamp('qc_verified_at')->nullable()->after('qc_verified_by');
            $table->foreign('qc_verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('single_product_manufacturing', function (Blueprint $table) {
            $table->dropForeign(['qc_verified_by']);
            $table->dropColumn(['qc_verified_by', 'qc_verified_at']);
        });

        Schema::table('batch_productions', function (Blueprint $table) {
            $table->dropForeign(['qc_verified_by']);
            $table->dropColumn(['qc_verified_by', 'qc_verified_at']);
        });

        Schema::table('manufacturing_reworks', function (Blueprint $table) {
            $table->dropForeign(['qc_verified_by']);
            $table->dropColumn(['qc_verified_by', 'qc_verified_at']);
        });
    }
}
