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
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return ProductResource::collection(Product::paginate(5));
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
        $data =  $request->validate([
            'name' => 'required|max:255|unique:products',
            'desc' => 'required',
            'content' => 'required|max:255',
            'image' => 'required',
            'id_brand' => 'required',
            'id_category' => 'required',
            'price' => 'required',
            'status' => 'required'
        ],['name.required'=> 'Yêu cầu tên sản phẩm',
            'desc.required'=> 'Yêu cầu mô tả',
            'content.required'=> 'Yêu cầu nội dung',
            'image.required'=> 'Yêu cầu hình ảnh',
            'price.required'=> 'Yêu cầu giá sản phẩm',
        ]);

        $image = $data['image'];
        $extension =  $image->getClientOriginalExtension();
        $name = time(). '_'.$image->getClientOriginalName();
        Storage::disk('uploads')->put($name,File::get($image));

        $product = new Product();

        $product->image = $name;
        $product->name = $data['name'];
        $product->desc = $data['desc'];
        $product->content = $data['content'];
        $product->id_brand = $data['id_brand'];
        $product->id_category = $data['id_category'];
        $product->status = $data['status'];
        $product->price = $data['price'];
        $product->save();

        return response([
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
        //return $product;
        return new ProductResource($product); //show trong mục chỉ định ProductResource
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
        $product->update($request->all());
        return response([
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
        return $product->delete();
    }
}
