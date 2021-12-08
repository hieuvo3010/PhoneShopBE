<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin, App\User, App\Order, App\Order_detail, App\Ship;
use App\Http\Resources\OrderResource,App\Http\Resources\Product\ProductResource;
use App\Http\Resources\UserResource;
use Validator;
use Carbon\Carbon;
class AdminController extends Controller
{
    //
     //
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:admins', ['except' => ['login', 'register','dashboard']]);
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
         
        // if (! auth()->user()->hasVerifiedEmail()) {
        //     return response()->json(['error' => 'Please verify your email address before logging in. You may request a new link here xyz.com if your verification has expired.'], 401);
        // }


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
            'email' => 'required|string|email|max:100|unique:admins|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $admins = Admin::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
        ));//->sendEmailVerificationNotification();
                //->sendEmailVerificationNotification()
        
        return response()->json([
            'message' => 'Admin successfully registered',
            'Admin' => $admins
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
    public function userProfile() {
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
            'admin' => auth()->user()
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

        $admin = Admin::where('id', $userId)->update(
                    ['password' => bcrypt($request->new_password)]
                );

        return response()->json([
            'message' => 'User successfully changed password',
            'admin' => $admin,
        ], 201);
    }

    public function show_account_user(Request $request){
        $account = User::all();
        return response()->json([
            'message' => 'All account users',
            'data' => new UserResource($account)
        ], 201);
    }

    public function show_all_order(Request $request){
        $orders = Order::with('user')->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'message' => 'All orders',
            'data' => new OrderResource($orders)
        ], 201);
    }

    public function show_detail_order(Request $request){
        $order_code = $request->query('order_code');
        $order= Order::where('order_code', $order_code)->first();
      
        $products_with_order = Order_detail::with('order','ship')->where('order_code', $order->order_code)->get();
        
        return response()->json([
            'message' => 'Detail order '.$order->order_code ,
            'data' => new ProductResource($products_with_order)
        ], 201);
    }

    public function update_order(Request $request){
        $order_code = $request->query('order_code');
        $order= Order::where('order_code', $order_code)->first();
        $order->fill($request->validate([
            'status' => 'required'
        ]));
        $order->save();
        return response([
            'message' => 'Update status order successfully',
            'data' => new OrderResource($order)
        ], 200);
    }

    public function delete_order(Request $request){
        $order_code = $request->query('order_code');
        $order= Order::where('order_code', $order_code)->first();
        $result = $order->destroy($order->id);
        if($result){
            return response([
                'message' => 'Delete order successfully'
            ], 200);
        }
    }

    public function dashboard(){
        $orders = Order::orderBy('created_at','DESC')->paginate(10);
        $totalSales = Order::where('status',3)->count();
        $totalRevenue = Order::where('status',3)->sum('total');
        $todaySales = Order::where('status',3)->whereDate('created_at',Carbon::today())->count();
        $todayRevenue = Order::where('status',3)->whereDate('created_at',Carbon::today())->sum('total');

        $dauthangnay = Carbon::now('Asia/Ho_Chi_Minh')->startOfMonth()->toDateTimeString();
        $dau_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->startOfMonth()->toDateTimeString();
        $cuoi_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->endOfMonth()->toDateTimeString();

        

        $sub7days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(7)->toDateTimeString();
        $sub365days = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateTimeString();

        $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
        
        if($_GET['dashboard_value']=='7ngay'){
            $get = Order::whereBetween('created_at',[$sub7days,$now])->orderBy('created_at','ASC')->get();
        }elseif($_GET['dashboard_value']=='thangtruoc'){

            $get = Order::whereBetween('created_at',[$dau_thangtruoc,$cuoi_thangtruoc])->orderBy('created_at','ASC')->get();
    
        }elseif($_GET['dashboard_value']=='thangnay'){
    
            $get = Order::whereBetween('created_at',[$dauthangnay,$now])->orderBy('created_at','ASC')->get();
    
        }else{
            $get = Order::whereBetween('created_at',[$sub365days,$now])->orderBy('created_at','ASC')->get();
        }

        return response([
            'orders' => new OrderResource($get),
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'todaySales' => $todaySales,
            'todayRevenue' => $todayRevenue,
        ], 200);
    }

}
