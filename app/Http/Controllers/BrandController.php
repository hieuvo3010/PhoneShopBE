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
        $bands = Brand::orderBy('id','DESC')->where('status', 1)->get();
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
        $slug = $request->query('slug');
        $brand = Brand::with('product')->where('slug',$slug)->get();
        if(isset($brand)){
            return new BrandResource($brand);
        }else{
            return response([
                'message' => 'This brand does not exist'
            ], 400);
        }
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
        $slug = $request->query('slug');
        $brand = Brand::where('slug',$slug)->first();
        if(isset($brand)){
            $brand->update($request->all());
            return response([
                'message' => 'Updated successfully',
                'data' => new BrandResource($brand)
            ], 201);
        }else{
            return response([
                'message' => 'This brand does not exist'
            ], 400);
        }
        
    }


    public function delete(Request $request)
    {
        //
        $slug = $request->query('slug');
        $brand = Brand::where('slug',$slug)->first();
        $result = $brand->destroy($brand->id);
        if($result){
            return response([
                'message' => 'Delete brand successfully'
            ], 201);
        }
    }

}
