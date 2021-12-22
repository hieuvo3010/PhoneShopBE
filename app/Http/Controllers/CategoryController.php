<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Http\Resources\CategoryResource;
class CategoryController extends Controller
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
        $categories = Category::orderBy('id','DESC')->where('status', 1)->paginate(10);
        return CategoryResource::collection($categories);
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
        $categories = new Category();
        $categories->fill($request->validate([
            'name' => 'required|max:255|unique:categories',
            'desc' => 'required',
            'slug'  => 'required|unique:categories',
        ]));
        $categories->save();

        return response([
            'data' => new CategoryResource($categories)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $Category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $id = $request->query('slug');
        $Category = Category::with('product')->where('slug',$slug)->get();
        return new CategoryResource($Category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $Category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $slug = $request->query('slug');
        $Category = Category::where('slug',$slug)->first();
        $Category->update($request->all());
        return response([
            'message' => 'Updated successfully',
            'data' => new CategoryResource($Category)
        ], 201);
    }


    public function delete(Request $request)
    {
        //
        $slug = $request->query('slug');
        $Category = Category::where('slug',$slug)->first();
        $result = $Category->destroy($Category->id);
        if($result){
            return response([
                'message' => 'Delete Category successfully'
            ], 201);
        }
    }
}
