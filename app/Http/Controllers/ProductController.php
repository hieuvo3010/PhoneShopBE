<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
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
        $products = Product::paginate(10);
        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $product->save();
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
        $product = Product::findOrFail($id)->with('brand')->get()->first();
        return new ProductResource($product); //show trong mục chỉ định ProductResource
    }

    public function update(Request $request)
    {
        //
        $id = $request->query('id');
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response([
            'message' => 'Update done',
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        //
        $id = $request->query('id');
        $product = Product::findOrFail($id);
        $product->destroy($id);
        return response()->json('null',204);
    }
}
