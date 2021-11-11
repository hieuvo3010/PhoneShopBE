<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'status', 'id_user','order_code','total','coupon'
    ];

    public function user(){
        return $this->belongsTo('App\User','id_user');
    }
   
    public function order_detail(){
        return $this->hasMany('App\Order_detail','id');
    }
}
