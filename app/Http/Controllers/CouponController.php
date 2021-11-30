<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;
use App\Http\Resources\CouponResource;
class CouponController extends Controller
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
        $coupons = Coupon::orderBy('id','DESC')->get();
        return CouponResource::collection($coupons);
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
        $data = new Coupon();
        $data->fill($request->validate([
            'name' => 'required|max:255|unique:coupons',
            'code' => 'required|unique:coupons',
            'quantity' => 'required',
            'number' => 'required',
        ]));
        $data->save();
        return response([
            'data' => new CouponResource($data)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $code = $request->query('coupon_code');
        $coupon = Coupon::where('code',$code)->get();
        if(isset($coupon)){
            return new CouponResource($coupon);
        }else{
            return response([
                'message' => 'This coupon does not exist'
            ], 400);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $code = $request->query('coupon_code');
        $coupon = Coupon::where('code',$code)->first();
        if(isset($coupon)){
            $coupon->update($request->all());
            return response([
                'message' => 'Updated successfully',
                'data' => new CouponResource($coupon)
            ], 201);
        }else{
            return response([
                'message' => 'This coupon does not exist'
            ], 400);
        }
    }

  
    public function delete(Request $request)
    {
        //
        $code = $request->query('coupon_code');
        $coupon = Coupon::where('code',$code)->first();
        if($coupon){
            $coupon->destroy($coupon->id);
            return response([
                'message' => 'Delete coupon successfully'
            ], 201);
        }else{
            return response([
                'message' => 'This coupon does not exist'
            ], 400);
        }
    }
}
