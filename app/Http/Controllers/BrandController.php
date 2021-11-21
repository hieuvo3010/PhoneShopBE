<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Http\Request;
use App\Http\Resources\BrandResource;

class BrandController extends Controller
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
        $bands = Brand::orderBy('id','DESC')->paginate(10);
        return BrandResource::collection($bands);
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
        $bands = new Brand();
        $bands->fill($request->validate([
            'name' => 'required|max:255|unique:brands',
            'desc' => 'required',
            'slug'  => 'required|unique:brands',
        ]));
        $bands->save();

        return response([
            'data' => new BrandResource($bands)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $id = $request->query('id');
        $brand = Brand::with('product')->where('id',$id)->get();
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $id = $request->query('id');
        $brand = Brand::findOrFail($id);
        $brand->update($request->all());
        return response([
            'message' => 'Updated successfully',
            'data' => new BrandResource($brand)
        ], 201);
    }


    public function delete(Request $request)
    {
        //
        $id = $request->query('id');
        $brand = Brand::findOrFail($id);
        $result = $brand->destroy($id);
        if($result){
            return response([
                'message' => 'Delete brand successfully'
            ], 201);
        }
    }

}
