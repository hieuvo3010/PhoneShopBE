<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    //
    protected $fillable = [
        'id_user','id_product'
    ];

    public function user(){
        return $this->belongsTo('App\User','id_user');
    }
    public function product(){
        return $this->belongsTo('App\Product','id_product');
     }
}
 