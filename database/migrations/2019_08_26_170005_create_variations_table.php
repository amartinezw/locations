<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->unsigned()->index();
            $table->string('sku', 32)->unique();
            $table->string('name');
            $table->integer('color_id')->unsigned()->index();
            $table->integer('stock')->unsigned();
            $table->integer('stock_blocked')->unsigned();
            $table->integer('discount')->unsigned();
            $table->decimal('price', 16, 2)->unsigned();
            $table->decimal('weight', 8, 2)->unsigned();
            $table->boolean('active')->index();
            $table->integer('order')->unsigned()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variations');
    }
}
