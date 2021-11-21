<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
    //
    protected $fillable = [
        'order_id', 'product_id','product_name','product_price','product_quantity','product_coupon','product_fee','order_code','product_image', 'ship_id','product_color'
    ];

    public function order(){
        return $this->belongsTo('App\Order','order_id');
    }
    public function product(){
        return $this->belongsTo('App\Product','product_id');
    }
    public function ship(){
        return $this->belongsTo('App\Ship', 'ship_id');
    }
}
