<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    protected $fillable = [
        'name','desc','discount','image','status','id_brand','price','slug','images_product','quantity','id_product_info'
    ];

    


    protected $casts  = [ 'images_product' => 'array'];

    public function color(){
        return $this->belongsTo('App\Colors_product', 'id_colors_product');
    }

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
     public function rating(){
        return $this->hasMany('App\Rating','id');
    }
    public function attributes(){
        return $this->belongsToMany('App\Attribute');
    }
 
}
