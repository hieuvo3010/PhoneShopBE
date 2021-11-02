<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HomeResource;
use App\Product;
use WithPagination;
class HomeController extends Controller
{
    //

    

    public function show_product_with_brand(Request $request,$id)
    {
        //
        $products = Product::where('status', '1')->with('brand')->where('id_brand', $id)->get();
        return response([
            'message' => 'Success',
            'data' => 
               HomeResource::collection($products),
        ], 201);
    }

    public function show_product_new()
    {
        //
        $products_new = Product::orderBy('created_at','DESC')->take(5)->paginate(5);
        return response([
            'message' => 'Success 10 products new',
            'data' => 
               HomeResource::collection($products_new),
        ], 201);
    }

    public function show_product(Request $request,$sort){ 
        if($sort == 'desc'){
            $products = Product::orderBy('id','desc')->paginate(5);
        }else{
            $products = Product::orderBy('id','asc')->paginate(5);
        }
        return response([
            'message' => 'Success products ascending',
            'data' => 
               HomeResource::collection($products),
        ], 201);
    }


}
