<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ship, App\Order, App\Order_detail;
use App\Http\Resources\ShipResource,App\Http\Resources\OrderResource,App\Http\Resources\OrderDetailResource;
class CheckoutController extends Controller
{
    // public function __construct() 
    // {
    //     //
    //     $this->middleware('auth:admins');
    // }
    //
    public function confirm_order(Request $request)
    {
        if(auth('users')->check()){
            $data =  $request->json()->all();
            $ship = new Ship();
            $ship->name = $data['name'];
            $ship->address = $data['address'];
            $ship->phone = $data['phone'];
            $ship->email = $data['email'];
            $ship->note = $data['note'];
            $ship->method = $data['method'];
            //$ship->fill($data);
            $ship->save();

            $order_code = substr(md5(microtime()),rand(0,26),5);
            $ship_id = $ship->id; //$shipping_id = DB::table('ships')->insertGetId($data);

            $order = new Order();
            $order->id_user = auth()->user()->id;
            $order->id_ship = $ship_id;
            $order->status = 1;
            $order->order_code = $order_code;
            $order->save();

            $order_id = $order->id;

            // insert order_Detail
            if($data['cart']){
                foreach($data['cart'] as $key => $cart){
                    $order_details = new Order_detail();
                    $order_details->order_code = $order->order_code;
                    $order_details->id_order = $order_id;
                    $order_details->id_product = $cart['product_id'];
                    $order_details->product_image = $cart['product_image'];
                    $order_details->product_name = $cart['product_name'];
                    $order_details->product_price = $cart['product_price'];
                    $order_details->product_quantity = $cart['product_qty'];
                    $order_details->product_coupon = $cart['order_coupon'];
                    $order_details->product_fee = $cart['order_fee'];
                    $order_details->save();
                }
            }
            
            $product_details = Order_detail::where('id_order',$order->id)->get();

            
            return response([
                'message' => 'Success',
                'ship' => new ShipResource($ship),
                'order' => new OrderResource($order),
                'order_detail' => 
                   OrderDetailResource::collection($product_details),
            ], 201);
        
        }else{
        return response([
            'status' => 401,
            'message' => 'Login with account User'
        ], 401);
        }
    } 
}
