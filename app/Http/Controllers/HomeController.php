<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HomeResource;
use App\Product, App\Attribute;
use WithPagination;
class HomeController extends Controller
{
    //
    public function show_product(Request $request){ 
        $this->pagesize = 10;
        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
            if($sort_by == 'latested'){
                $products = Product::orderBy('created_at','DESC')->paginate($this->pagesize);
            }elseif($sort_by == 'old'){
                $products = Product::orderBy('created_at','asc')->paginate($this->pagesize);
            }elseif($sort_by == 'price-desc'){
                $products = Product::orderBy('price','DESC')->paginate($this->pagesize);
            }elseif($sort_by == 'price-asc'){
                $products = Product::orderBy('price','ASC')->paginate($this->pagesize);
            }elseif($sort_by == 'price-discount'){
                $products = Product::orderBy('discount','DESC')->paginate($this->pagesize);
            }
        }
        if(isset($_GET['price_start']) && isset($_GET['price_end'])) {
            $price_start = $_GET['price_start'];
            $price_end = $_GET['price_end'];
            $products = Product::where('status', '1')->whereBetween('price',[$price_start,$price_end])->paginate($this->pagesize);
            
        }
        if(isset($_GET['id'])){
            $sort_by = $_GET['id'];
            $products = Product::where('status', '1')->with('brand')->where('brand_id', $id)->paginate($this->pagesize);
        }
        return response([
            'message' => 'Success products sort',
            'data' => 
               HomeResource::collection($products),
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
