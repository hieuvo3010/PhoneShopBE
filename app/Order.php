<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'status', 'id_user', 'id_ship','order_code'
    ];

    public function user(){
        return $this->belongsTo('App\User','id_user');
    }
    public function ship(){
        return $this->belongsTo('App\Ship', 'id_ship');
    }
    public function order_detail(){
        return $this->hasMany('App\Order_detail','id');
    }
}
