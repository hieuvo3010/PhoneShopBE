<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('screen');
            $table->string('rear_camera');
            $table->string('selfie_camera');
            $table->string('ram');
            $table->string('internal_memory');
            $table->string('cpu');
            $table->string('gpu');
            $table->string('battery');
            $table->string('sim');
            $table->string('os');
            $table->string('made');
            $table->string('time');
           
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
        Schema::dropIfExists('product_infos');
    }
}
