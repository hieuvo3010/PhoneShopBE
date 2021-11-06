<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category, App\Product_info;
use App\Brand;
use File;
use Storage;
use DB;
use Illuminate\Http\Request;
use App\Http\Resources\Product\ProductResource;
class ProductController extends Controller
{

    public function __construct() 
    {
        //
        $this->middleware('auth:admins', ['except' => ['index', 'show']]);
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
        $products = Product::with('brand','product_info')->orderBy('id','DESC')->paginate(10);
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
        $product_info = new Product_info();
        $product_info->fill($request->validate([
            'screen' => 'nullable',
            'rear_camera' => 'nullable',
            'selfie_camera' => 'nullable',
            'ram' => 'nullable',
            'internal_memory' => 'nullable',
            'cpu' => 'nullable',
            'gpu' => 'nullable',
            'battery' => 'nullable',
            'sim' => 'nullable',
            'os' => 'nullable',
            'made' => 'nullable',
            'time' => 'nullable',
        ]));
        $product_info->save();
        $product_info->id;
        
        $product = new Product();
        $product->fill($request->validate([
            'name' => 'required|max:255|unique:products',
            'desc' => 'required|max:255',
            'image' => 'required',
            'images_product' => 'nullable',
            'id_brand' => 'required',
            'discount' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'slug' => 'required|unique:products'
        ]));
        $product->id_product_info =  $product_info->id;
        $product->save();
        $id_product = $product->id;

        
        return response([
            'data' => (new ProductResource($product))
        ], 201);
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
        $id = $request->query('id');
        $product = Product::with('brand','product_info')->where('id',$id)->get();
        return new ProductResource($product); //show trong mục chỉ định ProductResource
    }

    public function update(Request $request)
    {
        //
        $id = $request->query('id');
        $product = Product::findOrFail($id);
       
        $product->update($request->all());
        
        $product_info = Product_info::findOrFail($product->id_product_info);
        $product_info->update($request->all());
        return response([
            'message' => 'Update done',
            'data' => new ProductResource($product)
        ], 201);
    }

   
    public function delete(Request $request)
    {
        //
        $id = $request->query('id');
        
        $product = Product::findOrFail($id);
        $result = $product->destroy($id);
        if($result){
            return response([
                'message' => 'Delete product successfully'
            ], 201);
        }
    }
}
