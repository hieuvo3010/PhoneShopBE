<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
    //
    protected $fillable = [
        'id_order', 'id_product','product_name','product_price','product_quantity','product_coupon','product_fee','order_code','product_image'
    ];

    public function order(){
        return $this->belongsTo('App\Order','id_order');
    }
    public function product(){
        return $this->belongsTo('App\Product','id_product');
    }
}
