<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name','desc','content','image','id_category','status','id_brand','price'
    ];

    public function category(){
        return $this->belongsTo('App\Category', 'id_category');
    }

    public function brand(){
        return $this->belongsTo('App\Brand','id_brand');
    }
}
