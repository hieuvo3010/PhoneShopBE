<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ship, App\Order, App\Order_detail, App\Product, App\Coupon;
use App\Http\Resources\ShipResource,App\Http\Resources\OrderResource,App\Http\Resources\OrderDetailResource;
use Carbon\Carbon,Mail;
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
                        if(!empty($data['note'])){
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
                            $order_details->save();
                            $total += $order_details->product_price*$order_details->product_quantity;
                        }
                        
                        

                        $order->total = round($total - (($coupon->number*$total)/100));
                        $order->coupon = $coupon->code;
                        $order->save();

                         //send mail confirm
                        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
                        $title_mail = "Đơn hàng xác nhận ngày".' '.$now;

                        $user = auth()->user();
                        $data['email'] = $user->email;

                        if(empty($order->coupon)){
                            $coupon_mail = 'Không có sử dụng';
                        }
                       
                        //lay gio hang
                        $order_details_mail = Order_detail::where('order_code', $order_code)->get();  
                            foreach($order_details_mail  as $key ){
                                $cart_array[] = array(
                                    'product_name' => $key->product_name,
                                    'product_price' => $key->product_price,
                                    'product_qty' => $key->product_quantity,
                                );
                            }

                        //lay shipping
                        $data['note'] ?
                            $shipping_array = array(
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'phone' => $data['phone'],
                                'address' => $data['address'],
                                'note' => $data['note'],
                                'method' => $data['method']
                            ) :
                            $shipping_array = array(
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'phone' => $data['phone'],
                                'address' => $data['address'],
                                'note' => $data['note'],
                                'method' => $data['method']
                            );
                        
                        //lay ma giam gia, lay coupon code
                        $ordercode_mail = array(
                            'coupon_code' => $order->coupon,
                            'order_code' => $order_code,
                            'total' => $order->total,
                        );
                        
                        Mail::send('order_mail',  ['cart_array'=>$cart_array, 'shipping_array'=>$shipping_array ,'code'=>$ordercode_mail] , function($message) use ($title_mail,$data){
                            $message->to($data['email'])->subject($title_mail);//send this mail with subject
                            $message->from($data['email'],$title_mail);//send from this mail
                        });
                    }else{
                        return response([
                            'message' => 'Coupon does not exist',
                        ], 400);
                    }
                }else{

                    $ship = new Ship();  // insert ship
                        $ship->name = $data['name'];
                        $ship->address = $data['address'];
                        $ship->phone = $data['phone'];
                        $ship->email = $data['email'];
                        if(!empty($data['note'])){
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
                            $order_details->save();
                            $total += $order_details->product_price*$order_details->product_quantity;
                        }
                        
                        
                        $order->total = $total;
                        $order->save();

                    

                         //send mail confirm
                        $now = Carbon::now('Asia/Ho_Chi_Minh')->format('d-m-Y H:i:s');
                        $title_mail = "Đơn hàng xác nhận ngày".' '.$now;

                        $user = auth()->user();
                        $data['email'] = $user->email;

                        if(empty($order->coupon)){
                            $coupon_mail = 'Không có sử dụng';
                        }
                       
                        //lay gio hang
                        $order_details_mail = Order_detail::where('order_code', $order_code)->get();  
                            foreach($order_details_mail  as $key ){
                                $cart_array[] = array(
                                    'product_name' => $key->product_name,
                                    'product_price' => $key->product_price,
                                    'product_qty' => $key->product_quantity,
                                );
                            }

                        //lay shipping
                        $data['note'] ?
                            $shipping_array = array(
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'phone' => $data['phone'],
                                'address' => $data['address'],
                                'note' => $data['note'],
                                'method' => $data['method']
                            ) :
                            $shipping_array = array(
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'phone' => $data['phone'],
                                'address' => $data['address'],
                                'note' => $data['note'],
                                'method' => $data['method']
                            );
                        
                        //lay ma giam gia, lay coupon code
                        $ordercode_mail = array(
                            'coupon_code' => $order->coupon,
                            'order_code' => $order_code,
                            'total' => $order->total,
                        );
                        
                        Mail::send('order_mail',  ['cart_array'=>$cart_array, 'shipping_array'=>$shipping_array ,'code'=>$ordercode_mail] , function($message) use ($title_mail,$data){
                            $message->to($data['email'])->subject($title_mail);//send this mail with subject
                            $message->from($data['email'],$title_mail);//send from this mail
                        });
                }
            }
            
            $product_details = Order_detail::where('order_id',$order->id)->get();

            
            return response([
                'message' => 'Success',
                'order' => new OrderResource($order),
            ], 201);
        
        }else{
        return response([
            'status' => 401,
            'message' => 'Login with account User'
        ], 401);
        }
    } 
}
