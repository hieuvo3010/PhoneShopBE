<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Category, App\Product_info,App\Brand,App\Product,App\Rating;
use App\Http\Resources\Product\ProductResource;
class ProductController extends Controller
{
    // public $attr;
    // public $inputs= [];
    // public $attributes_arr = [];
    // public $attributes_value;

    // public function add(){
    //     if(!in_array($this->attr,$this->attributes_arr)){
    //         array_push($this->attributes_arr,$this->attr);
    //     }
    // }
    public function __construct() 
    {
        //
        $this->middleware('auth:admins', ['except' => ['index', 'show','related_products']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //$products = Product::orderBy('id','DESC')->paginate(5);
        $products = Product::with('brand','product_info','attributes','ratings')->orderBy('id','DESC')->get();
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try {
            $product_info = new Product_info();
            $product_info->fill($request->all());
            $product_info->save();
            $product_info->id;
            
            $product = new Product();
            $product->fill($request->validate([
                'name' => 'required|max:255|unique:products',
                'desc' => 'required',
                'image' => 'required',
                'images_product' => 'nullable',
                // 'category_id' => 'required',
                'brand_id' => 'required',
                'discount' => 'required',
                'quantity' => 'required',
                'price' => 'required',
                'slug' => 'required|unique:products'
            ]));
            $product->product_info_id =  $product_info->id;
            $product->save();
            
            $product_id = $product->id;
            if(!empty($request->colors)){
                foreach ($request->get('colors') as $key => $value) {
                    $product->attributes()->attach($value);
                    $product->save();
                }
            }
            return response()->json([
                'status' => __('Create product successful'),
                'data' => (new ProductResource($product))
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => __('Create product failed'),
            ], Response::HTTP_BAD_REQUEST);
        }
        
        

        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        //return $product;
        $slug = $request->query('slug');
        $product = Product::with('brand','product_info','attributes','ratings')->where('slug',$slug)->first();
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

    public function update(Request $request)
    {
        //
        $slug = $request->query('slug');
        $product = Product::where('slug',$slug)->first();
        $product->update($request->all());
        $product_info = Product_info::findOrFail($product->product_info_id);
        $product_info->update($request->all());
        return response([
            'message' => 'Update done',
            'data' => new ProductResource($product)
        ], 201);
    }

   
    public function delete(Request $request)
    {
        //
        $slug = $request->query('slug');
        $product = Product::where('slug',$slug)->first();
        $product->attributes()->detach();
        $result = $product->destroy($product->id);
        if($result){
            return response([
                'message' => 'Delete product successfully'
            ], 201);
        }
    }

    public function related_products(Request $request){
        $difference = 1000000;
        $slug = $request->query('slug');
        $product_details = Product::where('slug', $slug)->first();
        
        if(!empty($product_details)){
            $related_products = Product::with('product_info')->whereBetween('price',[$product_details->price,$product_details->price + $difference])
            ->whereNotIn('id', [$product_details->id])->paginate(5);
            return response([
                'message' => 'Successfully',
                'data' => new ProductResource($related_products),
            ], 201);
        }else{
            return response([
                'message' => 'Slug not found',
            ], 201);
        }
        
    }
}
