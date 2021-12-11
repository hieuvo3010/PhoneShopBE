<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code');
            $table->integer('order_id')->unsigned();
            $table->integer('ship_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('product_image');
            $table->string('product_name');
            $table->string('product_price');
            $table->string('product_color');
            $table->integer('product_quantity'); 
            $table->integer('product_discount');    
            $table->integer('product_fee'); 
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
        Schema::dropIfExists('order_details');
    }
}
