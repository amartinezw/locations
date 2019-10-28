<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnActiveToWarehouseLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->string('active')->default(1);//
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->dropColumn('active');//
        });
    }
}
