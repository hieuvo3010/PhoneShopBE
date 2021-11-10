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
        $wishlists = Wishlist::with('product')->where("id_user", "=", $user->id)->orderby('id', 'desc')->paginate(10);
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

          $status=Wishlist::where('id_user',auth()->user()->id)
            ->where('id_product',$request->id_product)
            ->first();
        
        if(isset($status->id_user) and isset($request->id_product))
        {
            return response([
                'message' => 'This item is already in your wishlist!',
            ], 400);
        }
        else
        {
        $wishlist = new Wishlist();
        $wishlist->id_user = auth()->user()->id;
        $wishlist->id_product = $request->id_product;
        $wishlist->save();

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
        $user = Auth::user();
        $wishlist = Wishlist::findOrFail($id)->where('id_user',$user->id)->first();
        $result = $wishlist->destroy($id);
        if($result){
            return response([
                'message' => 'Delete product on wishlist'
            ], 201);
        }
    }
}
