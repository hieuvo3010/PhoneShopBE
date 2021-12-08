<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type')->nullable();
            $table->integer('discount');
            $table->integer('quantity');
            $table->integer('brand_id')->unsigned();
            $table->integer('category_id')->nullable()->unsigned();
            $table->integer('product_info_id')->unsigned();
            $table->text('desc');
            $table->integer('price');
            $table->string('slug');
            $table->string('image');
            $table->json('images_product')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('products');
    }
}
