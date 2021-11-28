<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User, App\Product, App\Wishlist;
use App\Http\Resources\WishlistResource;
class WishlistController extends Controller
{
    public function __construct() 
    {
        //
        $this->middleware('auth:users', ['except' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        $wishlists = Wishlist::with('product')->where("user_id", "=", $user->id)->orderby('id', 'desc')->paginate(10);
        return WishlistResource::collection($wishlists);
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
        $this->validate($request, array(
            'product_id' =>'required',
          ));

          $status=Wishlist::where('user_id',auth()->user()->id)
            ->where('product_id',$request->product_id)
            ->first();
        
        if(isset($status->user_id) and isset($request->product_id))
        {
            return response([
                'message' => 'This item is already in your wishlist!',
            ], 400);
        }
        else
        {
        $wishlist = new Wishlist();
        $wishlist->user_id = auth()->user()->id;
        $wishlist->product_id = $request->product_id;
        $wishlist->save();

        return response([
            'message' => 'Added to your wishlist.',
            'data' => (new WishlistResource($wishlist))
        ], 201);
        }
    }

    public function delete_product_on_wishlist(Request $request)
    {
        //
        $product_id = $request->query('product_id');
        $user = Auth::user();
        $wishlist = Wishlist::where('user_id',$user->id)->where('product_id',$product_id)->first();
        if($wishlist){
            $wishlist->destroy($wishlist->id);
            return response([
                'message' => 'Delete product on wishlist'
            ], 200);
        }else{
            return response([
                'message' => 'This product does not exist in wishlist'
            ], 400);
        }
    }
}
