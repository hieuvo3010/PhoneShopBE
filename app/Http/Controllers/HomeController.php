<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HomeResource;
use App\Product, App\Attribute, App\Rating;
use App\Http\Resources\Product\ProductResource;
use WithPagination;
class HomeController extends Controller
{
    //

    public function show_product_with_brand(Request $request)
    {
        //
        $id = $request->query('id');
        $products = Product::where('status', '1')->with('brand')->where('brand_id', $id)->get();
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
            $products = Product::all();
         
            foreach($products as $product){
                $ratings = Rating::where('product_id', $product->id)->get();
                $ratingValues = [];

                foreach ($ratings as $aRating) {
                    $ratingValues[] = $aRating->star;
                }
                if(!empty($aRating->star)){
            
                    $ratingAverage = collect($ratingValues)->sum() / $ratings->count();
                
                    $one_star = Rating::where('product_id', $product->id)->where('star', 1)->count();
                    $two_star = Rating::where('product_id', $product->id)->where('star', 2)->count();
                    $three_star = Rating::where('product_id', $product->id)->where('star', 3)->count();
                    $four_star = Rating::where('product_id', $product->id)->where('star', 4)->count();
                    $five_star = Rating::where('product_id', $product->id)->where('star', 5)->count();
                    return response()->json([
                        'data' => new ProductResource($product),
                        'star_avg' => $ratingAverage,
                        'one_star' => $one_star,
                        'two_star' => $two_star,
                        'three_star' => $three_star,
                        'four_star' => $four_star,
                        'five_star' => $five_star,
                    ], 201);
                }else{
               
                    return response()->json([
                        'data' => new ProductResource($product),
                    ], 201);
                }
            }
            
        }elseif($sort == 'old'){
            $products = Product::with('ratings')->orderBy('created_at','asc')->paginate($this->pagesize);
        }elseif($sort == 'price-desc'){
            $products = Product::with('ratings')->orderBy('price','DESC')->paginate($this->pagesize);
        }elseif($sort == 'price-asc'){
            $products = Product::with('ratings')->orderBy('price','ASC')->paginate($this->pagesize);
        }elseif($sort == 'price-discount'){
            $products = Product::with('ratings')->orderBy('discount','ASC')->paginate($this->pagesize);
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
