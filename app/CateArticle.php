<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CateArticle extends Model
{
    //
    
    protected $fillable = [
        'name', 'desc', 'status','slug'
    ];

    public function article(){
        return $this->hasMany('App\Article');
    }
}
