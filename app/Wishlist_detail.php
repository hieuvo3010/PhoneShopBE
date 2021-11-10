<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist_detail extends Model
{
    //
    protected $fillable = [
        'id_wishlist', 'id_product','product_name','product_price','product_image','product_discount'
    ];
    public function wishlist(){
        return $this->belongsTo('App\Wishlist','id_wishlist');
    }
    public function product(){
        return $this->belongsTo('App\Product','id_product');
    }
}
