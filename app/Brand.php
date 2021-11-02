<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    protected $fillable = [
        'name', 'desc', 'status','slug'
    ];

    public function product(){
        return $this->hasMany('App\Product');
    }
}
