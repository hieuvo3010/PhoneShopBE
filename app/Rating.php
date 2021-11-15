<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    //
    protected $fillable = [
        'id_product','id_user','content','star'
    ];
    public function user(){
        return $this->belongsTo('App\User','id_user');
    }
    public function product(){
        return $this->belongsTo('App\Product','id_product');
    }

}
