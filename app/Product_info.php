<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_info extends Model
{
    //
    protected $fillable = [
        'screen', 'rear_camera', 'selfie_camera','ram','internal_memory','cpu','gpu','battery','sim','os','made','time'
    ];

    
    public function product(){
        return $this->hasOne('App\Product');
    }

   
}
