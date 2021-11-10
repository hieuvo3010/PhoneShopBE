<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    protected $fillable = [
        'name','desc','discount','image','status','id_brand','price','slug','images_product','quantity','id_product_info'
    ];

    protected $casts  = [ 'images_product' => 'array' ];
    public function category(){
        return $this->belongsTo('App\Category', 'id_category');
    }

    public function brand(){
        return $this->belongsTo('App\Brand','id_brand');
    }
    public function product_info(){
        return $this->belongsTo('App\Product_info','id_product_info');
    }
    public function wishlist(){
        return $this->hasOne('App\Wishlist');
     }
   
}
