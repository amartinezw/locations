<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRackIdColumnWarehouselocationtable extends Migration
{
    public function up()
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->unsignedInteger('rack_id');
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
            $table->dropColumn('rack_id');
        });
    }
}