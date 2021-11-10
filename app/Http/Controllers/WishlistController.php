<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User, App\Product, App\Wishlist,App\Wishlist_detail;
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
    public function index(Request $request)
    {
        //
        $user = auth()->user();
        $userId = auth()->user()->id;

         //$wl = Wishlist_detail::with('wishlist','product')->where('id_wishlist',$wishlist->id)->get();
        $wl = Wishlist::where('id_user',$userId)->first();
        $wishlists = Wishlist_detail::where("id_wishlist", "=", $wl->id)->get(); //->orderby('created_at', 'desc')->paginate(10);
        return WishlistResource::collection($wishlists);
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
        $this->validate($request, array(
            'id_product' =>'required',
          ));

          $status=Wishlist_detail::with('wishlist')
            ->where('id_product',$request->id_product)
            ->first();
        //
        if(isset($status->id_product) and isset($request->id_product))
        {
            return response([
                'message' => 'This item is already in your wishlist!',
            ], 400);
        }
        else
        {
        
        
        $wishlist = new Wishlist();
        $wishlist->id_user = auth()->user()->id;
        $wishlist->save();

        $wishlist_detail = new Wishlist_detail();
        $wishlist_detail->id_product = $request->id_product;
        $product = Product::find($request->id_product);
        $wishlist_detail->product_image = $product->image;
        $wishlist_detail->product_name = $product->name;
        $wishlist_detail->product_discount = $product->discount;
        $wishlist_detail->product_price = $product->price;
        $wishlist_detail->id_wishlist =  $wishlist->id;
        $wishlist_detail->save();

        
     
        return response([
            'message' => 'Added to your wishlist.',
            'data' => (new WishlistResource($wishlist))
        ], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function delete(Request $request)
    {
        //
        $id = $request->query('id');
        
        $wishlist = Wishlist::findOrFail($id);
        $result = $wishlist->destroy($id);
        if($result){
            return response([
                'message' => 'Item successfully deleted'
            ], 201);
        }
    }
}
