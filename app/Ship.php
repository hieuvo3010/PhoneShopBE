<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    //
    protected $fillable = [
        'name', 'email', 'phone','address','note','method'
    ];

    public function order_detail(){
        return $this->hasOne('App\Order_detail');
    }
}
