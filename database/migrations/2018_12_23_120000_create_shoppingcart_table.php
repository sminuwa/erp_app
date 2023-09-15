<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingcartTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->string('identifier');
            $table->string('instance');
            $table->longText('content');
            $table->timestamps();

            //$table->unique(['identifier', 'instance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('shopping_carts');
    }
}
