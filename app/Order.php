<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'status', 'user_id','ship_id','order_code','total','coupon','coupon_code'
    ];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
   
    public function order_detail(){
        return $this->hasMany('App\Order_detail','id');
    }
    public function ship(){
        return $this->belongsTo('App\Ship', 'ship_id');
    }
}
