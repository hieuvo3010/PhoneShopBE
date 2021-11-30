<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ship, App\Order, App\Order_detail, App\Product, App\Coupon;
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
            // insert order_Detail
            if($data['cart']){   // check cart
                if($request->coupon_code){ // check coupon 
                    $coupon = Coupon::where('code', $request->coupon_code)->first();
                    if($coupon){  // exist coupon
                        $ship = new Ship();  // insert ship
                        $ship->name = $data['name'];
                        $ship->address = $data['address'];
                        $ship->phone = $data['phone'];
                        $ship->email = $data['email'];
                        if(asset($data['note'])){
                            $ship->note = $data['note'];
                        }
                        $ship->method = $data['method'];
                        //$ship->fill($data);
                        $ship->save();

                        $order_code = substr(md5(microtime()),rand(0,26),5);
                        $ship_id = $ship->id; //$shipping_id = DB::table('ships')->insertGetId($data);

                        $order = new Order(); // insert order
                        $order->user_id = auth()->user()->id;
                        $order->status = 1;
                        $order->order_code = $order_code;
                        $order->save();

                        $order_id = $order->id;
                        $total = 0;
                        foreach($data['cart'] as $key => $cart){
                            $order_details = new Order_detail();
                            $order_details->order_code = $order->order_code;
                            $order_details->order_id = $order_id;
                            $order_details->product_id = $cart['product_id'];
                            $order_details->ship_id = $ship_id;
                            $product = Product::findOrFail($order_details->product_id);
                            $attributes = $product->attributes;
                            
                            foreach ($attributes as $value){
                                if($value->id == $cart['product_color']){
                                    $order_details->product_color = $value->name;
                                }
                            }
                            $order_details->product_image = $product->image;
                            $order_details->product_name = $product->name;
                            if($product->discount){
                                $order_details->product_price = round($product->price - (($product->price*$product->discount)/100)) ;
                            }else{
                                $order_details->product_price = $product->price;
                            }
                            $order_details->product_quantity = $cart['product_quantity'];
                            
                            // $order_details->product_coupon = $cart['order_coupon'];
                            $order_details->product_fee = 0;
                            
                        }
                        $order_details->save();
                        $total += $order_details->product_price*$order_details->product_quantity;

                        $order->total = round($total - (($coupon->number*$total)/100));
                        $order->coupon = $coupon->code;
                        $order->save();
                    }else{
                        return response([
                            'message' => 'Coupon does not exist',
                        ], 400);
                    }
                }else{
                    $order_details->save();
                    $total += $order_details->product_price*$order_details->product_quantity;

                    $order->total = $total;
                    $order->save();
                }
            }
            
            $product_details = Order_detail::where('order_id',$order->id)->get();

            
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
