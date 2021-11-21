<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    //
    protected $fillable = [
        'product_id','user_id','content','star'
    ];


    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
    public function product(){
        return $this->belongsTo('App\Product','product_id');
    }

}
