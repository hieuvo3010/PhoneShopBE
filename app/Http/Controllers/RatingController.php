<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rating, App\Product;
use App\Http\Resources\RatingResource;
class RatingController extends Controller
{
    public function __construct() 
    {
        //
        $this->middleware('auth:users', ['except' => ['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        
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
        $rating = new Rating();
        $id = $request->input('id');
        $c =Rating::where('id_user',auth()->user()->id)->where('id_product',$id)->first();
        if(isset($c)){
            return response([
               'message' => 'You rated it'
            ], 404);
        }else{
            $product = Product::find($id);
            $rating->id_product = $product->id;
            $rating->id_user = auth()->user()->id;
            $rating->content = $request->content;
            $rating->star = $request->star;
            $rating->save();
            return response([
                'data' => (new RatingResource($rating))
            ], 201);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $id = $request->input('id');
        
        $rating = Rating::where('id_product', $id)->get();
      
        return RatingResource::collection($rating);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
