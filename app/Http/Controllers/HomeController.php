<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HomeResource;
use App\Product, App\Attribute, App\Brand;
use WithPagination;
class HomeController extends Controller
{
    //
    public function show_product(Request $request){ 
        $this->pagesize = 10;
        $s = Product::with('brand');
     
        
        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
            if($sort_by == 'discount'){
                $s->orderBy('discount','DESC');
            }elseif($sort_by == 'desc'){
                $s->orderBy('price','DESC');
            }elseif($sort_by == 'asc'){
                $s->orderBy('price','ASC');
            }
        }
        


        if(isset($_GET['price_start']) && isset($_GET['price_end'])) {
            $price_start = $_GET['price_start'];
            $price_end = $_GET['price_end'];
            $s->whereBetween('price',[$price_start,$price_end])->paginate($this->pagesize);
            
        }
        if(isset($_GET['brand_id'])){
            if(isset($_GET['brand_id1']) && isset($_GET['brand_id2'])){
                $s->whereIn('brand_id', [$_GET['brand_id'],$_GET['brand_id1'],$_GET['brand_id2']]);
            }elseif(isset($_GET['brand_id2'])){
                $s->whereIn('brand_id', [$_GET['brand_id'],$_GET['brand_id2']]);
            }elseif(isset($_GET['brand_id1'])){
                $s->whereIn('brand_id', [$_GET['brand_id'],$_GET['brand_id1']]);
            }else{
                $s->where('brand_id', [$_GET['brand_id']]);
            }
            //$products = Product::where('status', '1')->with('brand')->where('brand_id', $id)->paginate($this->pagesize);
        }
        return response([
            'message' => 'Success products sort',
            'data' => 
               HomeResource::collection($s->get()),
        ], 201);
    }

    public function show_color_products(Request $request){
        $colors = Attribute::all();
        return response([
            'message' => 'All colors product',
            'data' => 
               HomeResource::collection($colors),
        ], 201);
    }

}
