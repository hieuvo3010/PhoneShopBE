<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    protected $fillable = [
        'name', 'desc', 'status','slug','cate_article_id','image'
    ];

    public function cate_article(){
        return $this->belongsTo('App\CateArticle','cate_article_id');
    }
}
