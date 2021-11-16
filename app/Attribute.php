<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    //
    protected $fillable = [
        'name'
    ];
    public function products(){
        return $this->belongsToMany('App\Product');
    }
    public function teamMembers(){
        return $this->belongsToMany('App\Product')->withPivot('id');;
    }
}
