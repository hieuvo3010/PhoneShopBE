<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use App\Category;
use App\Brand;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        //
            
            'name' => $faker->word,
            'id_category' => function(){
                return Category::all()->random();
            },
            'id_brand' => function(){
                return Brand::all()->random();
            },
            'desc' => $faker->paragraph,
            'content' => $faker->paragraph,
            'price' => $faker->numberBetween(100,1000),
            'image' => $faker->imageUrl($width = 640, $height = 480),
            'status' => 1,
    ];
});
