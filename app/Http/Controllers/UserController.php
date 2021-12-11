<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order, App\Order_detail;
use Illuminate\Support\Facades\Auth;
use App\User, Hash;
use App\Http\Resources\UserResource;
use App\Http\Resources\OrderResource,App\Http\Resources\Product\ProductResource;
use Validator;

class UserController extends Controller
{
    //
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:users', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
         
        if (! auth()->user()->hasVerifiedEmail()) {
            return response()->json(['error' => 'Please verify your email address before logging in. You may request a new link here xyz.com if your verification has expired.'], 401);
        }


        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users|unique:admins',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
        ))->sendEmailVerificationNotification();
                //->sendEmailVerificationNotification()
        
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token = $this->createNewToken($token);
        return response()->json([
            'message' => 'User successfully registered',
            // 'token' => $token
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(Request $request) {
        
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function changePassWord(Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        if (!(Hash::check($request->get('old_password'), Auth::user()->password))) {
            // The passwords matches
            return response()->json([
                'error' => 'Your current password does not matches with the password you provided. Please try again.',
            ], 400);
        }

        $user = User::where('id', $userId)->update(
                    ['password' => bcrypt($request->new_password)]
                );

        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $user,
        ], 201);
    }

    public function updateProfile(Request $request) {
        try {
            $validator = Validator::make($request->all(),[
                'name' => 'required|max:255',
                'image' => 'required',
                'dob' => 'required',
                'sex' => 'required',
                'email' => 'required',
            ]);
            if($validator->fails()){
                $error = $validator->errors()->all()[0];
                return response()->json(['status' => false, 'message' => $error,'data' => []],422);
            }else{
                $user = auth()->user();
                $user->update([
                    'name' => $request->name,
                    'image' => $request->image,
                    'dob' => $request->dob,
                    'sex' => $request->sex,
                    'email' => $request->email,
                ]);
            }
        }catch (ValidationException $e){
            return response()->json(['status' => 'false','message' =>$e->getMessage(),'data' =>[]],500 );
        }
      
        return response([
            'message' => 'Update profile successfully',
            'data' => new UserResource($user)
        ], 201);
    }

    public function show_order_detail(Request $request){
        $userId = auth()->user()->id;
        $order_code = $request->query('order_code');
        $order= Order::with('order_detail')->where('user_id', $userId)->where('order_code', $order_code)->first();
        $products_with_order = Order_detail::with('ship','order')->where('order_code', $order->order_code)->get();
        
        return response()->json([
            'message' => 'Detail order '.$order->order_code ,
            'data' => new ProductResource($products_with_order)
        ], 201);
    }

    public function show_all_order(Request $request){
        $userId = auth()->user()->id;
        $orders = Order::where('user_id', $userId)->orderBy('created_at','DESC')->get();
        return response()->json([
            'message' => 'All orders',
            'data' => new OrderResource($orders)
        ], 201);
    }

    public function delete_order(Request $request){
        $order_code = $request->query('order_code');
        $order= Order::where('order_code', $order_code)->first();
        if($order->status == 2 || $order->status == 3){
            return response([
                'message' => 'Order has been shipped or completed',
            ], 400);
        }else{
            $order->destroy($order->id);
            return response([
                'message' => 'Delete order successfully'
            ], 200);
        }
    }
}
