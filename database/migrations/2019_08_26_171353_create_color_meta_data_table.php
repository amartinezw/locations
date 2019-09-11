<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColorMetaDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_meta_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            
            $table->datetime('reviewed_at')->nullable();
            $table->integer('reviewed_by')->unsigned()->index();

            $table->string('language', 4)->index();

            $table->integer('color_id')->unsigned()->index();
            $table->string('name', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('color_meta_data');
    }
}
