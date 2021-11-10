<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWishlistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_user');
            $table->integer('id_product');
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
        Schema::dropIfExists('wishlists');
    }
}
// Schema::create('wishlist_details', function (Blueprint $table) {
//     $table->bigIncrements('id');
//     $table->integer('id_wishlist');
//     $table->integer('id_product');
//     $table->string('product_image');
//     $table->string('product_name');
//     $table->string('product_price');
//     $table->string('product_discount');    
//     $table->timestamps();
// });