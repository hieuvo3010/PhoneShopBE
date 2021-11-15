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

            $rating->id_product = $data['product_id'];
            $rating->rating = $data['index'];

            return response([
                'data' => (new RatingResource($rating)),
                'rating_avg' => $p->avgRating,
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
        
        $ratings = Rating::with('user')->where('id_product', $id)->get();
        $ratingValues = [];

        foreach ($ratings as $aRating) {
            $ratingValues[] = $aRating->star;
        }
    
        $ratingAverage = collect($ratingValues)->sum() / $ratings->count();
        
        $one_star = Rating::where('id_product', $id)->where('star', 1)->count();
        $two_star = Rating::where('id_product', $id)->where('star', 2)->count();
        $three_star = Rating::where('id_product', $id)->where('star', 3)->count();
        $four_star = Rating::where('id_product', $id)->where('star', 4)->count();
        $five_star = Rating::where('id_product', $id)->where('star', 5)->count();
        return response()->json([
                'data' => RatingResource::collection($ratings),
                'star_avg' => $ratingAverage,
                'one_star' => $one_star,
                'two_star' => $two_star,
                'three_star' => $three_star,
                'four_star' => $four_star,
                'five_star' => $five_star,
            ], 201);
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
