<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('warehouse_id');
            $table->integer('rack');
            $table->integer('block');
            $table->integer('level');
            $table->integer('side');
            $table->string('mapped_string');
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
        Schema::dropIfExists('warehouse_locations');
    }
}
