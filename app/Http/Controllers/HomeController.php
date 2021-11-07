<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HomeResource;
use App\Product;
use WithPagination;
class HomeController extends Controller
{
    //
   
    

    public function show_product_with_brand(Request $request)
    {
        //
        $id = $request->query('id');
        $products = Product::where('status', '1')->with('brand')->where('id_brand', $id)->get();
        return response([
            'message' => 'Success',
            'data' => 
               HomeResource::collection($products),
        ], 201);
    }
   


    public function show_product_between_price(Request $request){ 
        $this->pagesize = 10;
        $price_start = $request->query('price_start');
        $price_end = $request->query('price_end');
        
        $products = Product::where('status', '1')->whereBetween('price',[$price_start,$price_end])->orderBy('id', 'asc')->paginate($this->pagesize);
      
        return response([
            'message' => 'Success products sort',
            'data' => 
               HomeResource::collection($products),
        ], 201);
    }


    public function show_product(Request $request){ 
        $this->pagesize = 10;
        $sort = $request->query('sort');
        
        if($sort == 'new'){
            $products = Product::orderBy('created_at','DESC')->paginate($this->pagesize);
        }elseif($sort == 'old'){
            $products = Product::orderBy('created_at','asc')->paginate($this->pagesize);
        }elseif($sort == 'price-desc'){
            $products = Product::orderBy('price','DESC')->paginate($this->pagesize);
        }elseif($sort == 'price-asc'){
            $products = Product::orderBy('price','ASC')->paginate($this->pagesize);
        }elseif($sort == 'price-discount'){
            $products = Product::orderBy('discount','ASC')->paginate($this->pagesize);
        }
        return response([
            'message' => 'Success products sort',
            'data' => 
               HomeResource::collection($products),
        ], 201);
    }


}
