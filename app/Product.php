<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //

    protected $fillable = [
        'name','desc','discount','image','status','brand_id','price','slug','images_product','quantity','product_info_id'
    ];

    protected $casts  = [ 'images_product' => 'array'];

    public function category(){
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function brand(){
        return $this->belongsTo('App\Brand','brand_id');
    }
    public function product_info(){
        return $this->belongsTo('App\Product_info','product_info_id');
    }
    public function wishlist(){
        return $this->hasOne('App\Wishlist');
     }
     public function ratings(){
        return $this->hasMany('App\Rating');
    }
    public function attributes(){
        return $this->belongsToMany('App\Attribute');
    }
 
}
