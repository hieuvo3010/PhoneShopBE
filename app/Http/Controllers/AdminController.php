<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin, App\User, App\Order, 
    App\Order_detail, App\Ship, App\Product, App\Article, 
    App\Category, App\Brand, App\Coupon, App\CateArticle;
use App\Http\Resources\OrderResource,App\Http\Resources\Product\ProductResource;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\UserResource;
use Validator;
use Carbon\Carbon, DB;
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
        $this->middleware('auth:admins', ['except' => ['login', 'register']]);
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

    public function update_user(Request $request){
        $email = $request->query('email');
        $user = User::where('email', $email)->first();
        $user->update([
            'status' => $request->status
        ]);
        return response()->json([
            'user' => $user,
        ], 201);
    }
   
    public function show_all_order(Request $request){
      
        $dauthangnay = Carbon::now('Asia/Ho_Chi_Minh')->startOfMonth()->toDateTimeString();
        $dau_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->startOfMonth()->toDateTimeString();
        $cuoi_thangtruoc = Carbon::now('Asia/Ho_Chi_Minh')->subMonth()->endOfMonth()->toDateTimeString();

        $sub7days = Carbon::now('Asia/Ho_Chi_Minh')->subDays(7)->toDateTimeString();
        $sub1days = Carbon::now('Asia/Ho_Chi_Minh')->subDay()->toDateTimeString();
        $sub365days = Carbon::now('Asia/Ho_Chi_Minh')->subDays(365)->toDateTimeString();

        $now = Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();

        $s = Order::with('user','ship');

        if(isset($_GET['status'])){ // check brand
            $status = $_GET['status'];
            if($status == 'cancel'){
                $s->where('status',0);
            }elseif($status == 'processing'){
                $s->where('status',1);
            }elseif($status == 'delivering'){
                $s->where('status',2);
            }elseif($status == 'complete'){
                $s->where('status',3);
            }elseif($status == 'failure'){
                $s->where('status',4);
            }
        }
        if(isset($_GET['filter'])){
            $filter = $_GET['filter'];
            if($filter == 'week'){
                $s->whereBetween('created_at',[$sub7days,$now]);
            }elseif($filter == 'today'){
                $s->whereBetween('created_at',[$sub1days,$now]);
            }elseif($filter == 'last-month'){
                $s->whereBetween('created_at',[$dau_thangtruoc,$cuoi_thangtruoc]);
            }elseif($filter == 'month'){
                $s->whereBetween('created_at',[$dauthangnay,$now]);
            }elseif($filter == 'year'){
                $s->whereBetween('created_at',[$sub365days,$now]);
            }
        }
        $orders = $s->orderBy('id','DESC')->get();
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
 
        // Create a new Laravel collection from the array data
        $itemCollection = collect($orders);
 
        // Define how many items we want to be visible in each page
        $perPage = 10;
 
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();
 
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
 
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        // if(isset($_GET['filter']) == 'week'){
        //     $get = Order::whereBetween('created_at',[$sub1days,$now])->orderBy('created_at','DESC')->get();
        // }elseif(isset($_GET['filter']) =='today'){
        //     $get = Order::whereBetween('created_at',[$dau_thangtruoc,$cuoi_thangtruoc])->orderBy('created_at','DESC')->get();
        // }elseif(isset($_GET['filter']) =='last-month'){
        //     $get = Order::whereBetween('created_at',[$dau_thangtruoc,$cuoi_thangtruoc])->orderBy('created_at','DESC')->get();
        // }elseif(isset($_GET['filter']) =='month'){
        //     $get = Order::whereBetween('created_at',[$dauthangnay,$now])->orderBy('created_at','DESC')->get();
        // }elseif(isset($_GET['filter']) =='year'){
        //     $get = Order::whereBetween('created_at',[$dauthangnay,$now])->orderBy('created_at','DESC')->get();
        // }elseif(isset($_GET['filter']) =='cancel'){
        //     $get = Order::where('status',0)->orderBy('created_at','DESC')->get();
        // }elseif(isset($_GET['filter']) =='processing'){
        //     $get = Order::where('status',1)->orderBy('created_at','DESC')->get();
        // }elseif(isset($_GET['filter']) =='delivering'){
        //     $get = Order::where('status',2)->orderBy('created_at','DESC')->get();
        // }
        // elseif(isset($_GET['filter']) =='complete'){
        //     $get = Order::where('status',3)->orderBy('created_at','DESC')->get();
        // }else{
        //     $get = Order::whereBetween('created_at',[$sub365days,$now])->orderBy('created_at','DESC')->get();
        // }
        return response()->json([
            'message' => 'All orders',
            'data' => new OrderResource($orders)
        ], 201);
    }

    public function show_detail_order(Request $request){
        $order_code = $request->query('order_code');
        $order= Order::where('order_code', $order_code)->first();
      
        $products_with_order = Order_detail::with('order')->where('order_code', $order->order_code)->get();
        
        return response()->json([
            'message' => 'Detail order '.$order->order_code ,
            'data' => new ProductResource($products_with_order)
        ], 201);
    }

    public function update_order(Request $request){
        $order_code = $request->query('order_code');
        $order= Order::where('order_code', $order_code)->first();
        $data = $request->validate([
            'status' => 'required'
        ]);
        $order->update($data);
        if($order->status == 3){
            $order_d = Order_detail::where('order_code', $order_code)->get();
            foreach($order_d as $value) {
                $product = Product::find($value->product_id);
                $updateDetails = [
                    'quantity' => $product->quantity - $value->product_quantity,
                    'sold' => $product->sold + $value->product_quantity
                ];
                $product->update($updateDetails);
            }
            
        }
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
        $totalProduct = 0;
        $totalProductSold = 0;
        $products = Product::all();

        foreach($products as $product) {
            $totalProduct += $product->quantity;
            $totalProductSold += $product->sold;
        }
        $orders = Order::orderBy('created_at','DESC')->paginate(10);
        $totalSales = Order::where('status',3 )->count();
        $totalRevenue = Order::where('status',3 )->sum('total');
        $todaySales = Order::where('status',3)->whereDate('created_at',Carbon::today())->count();
        $todayRevenue = Order::where('status',3)->whereDate('created_at',Carbon::today())->sum('total');

        $c = Order::where('status',3)->first();
        if($c){
            $orders_by_month = Order::select(DB::raw("COUNT(*) as count"))
                            ->whereYear('updated_at', date('Y'))
                            ->where('status', 3)
                            ->groupBy(DB::raw("Month(updated_at)"))
                            ->pluck('count');
            $months =  Order::select(DB::raw("Month(updated_at) as month"))
                            ->whereYear('updated_at', date('Y'))
                            ->groupBy(DB::raw("Month(updated_at)"))
                            ->pluck('month');
            $data = [0,0,0,0,0,0,0,0,0,0,0,0];
            foreach ($months as $index => $month){
                --$month;
                $data[$month] = $orders_by_month[$index];
            }        
            return response([
                'sales' => $data,
                'totalProduct' => $totalProduct,
                'totalProductSold' => $totalProductSold,
                'totalSales' => $totalSales,
                'totalRevenue' => $totalRevenue,
                'todaySales' => $todaySales,
                'todayRevenue' => $todayRevenue,
            ], 200);
        }else{
            return response([
                'sales' => [0,0,0,0,0,0,0,0,0,0,0,0],
                'totalProduct' => $totalProduct,
                'totalProductSold' => $totalProductSold,
                'totalSales' => $totalSales,
                'totalRevenue' => $totalRevenue,
                'todaySales' => $todaySales,
                'todayRevenue' => $todayRevenue,
            ], 200);
        }
        
    }

    // public function search(Request $request, $type){
    //     $data = $request->query('data');
    //     switch ($type) {
    //         case "categories":
    //             $drivers = Category::where('name', 'like', "%{$data}%")
    //             ->orWhere('slug', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None category'
    //             ]);
    //           break;
    //         case "brands":
    //             $drivers = Brand::where('name', 'like', "%{$data}%")
    //             ->orWhere('slug', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None brands'
    //             ]);
    //           break;
    //         case "coupons":
    //             $drivers = Coupon::where('name', 'like', "%{$data}%")
    //             ->orWhere('code', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None coupons'
    //             ]);
    //           break;
    //         case "products":
    //             $drivers = Product::where('name', 'like', "%{$data}%")
    //             ->orWhere('slug', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None coupons'
    //             ]);
    //           break;
    //         case "orders":
    //             $drivers = Order::where('order_code', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None orders'
    //             ]);
    //           break;
    //         case "users":
    //             $drivers = User::where('name', 'like', "%{$data}%")
    //             ->orWhere('email', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None users'
    //             ]);
    //          break;
    //         case "category-aticles":
    //             $drivers = CateArticle::where('name', 'like', "%{$data}%")
    //             ->orWhere('slug', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None category-caticles'
    //             ]);
    //           break;  
    //         case "articles":
    //             $drivers = CateArticle::where('name', 'like', "%{$data}%")
    //             ->orWhere('slug', 'like', "%{$data}%")
    //             ->paginate(10);
    //             if($drivers){
    //                 return response([
    //                     'data' => $drivers
    //                 ]);
    //             }
    //             return response([
    //                 'data' => 'None category-caticles'
    //             ]);
    //           break;
    //         default:
    //           echo "URL does not exist";
    //     }
    // }

    public function show(Request $request, $type){
        switch ($type) {
            case "brands":
                $bands = Brand::orderBy('id','DESC')->get();
                return ProductResource::collection($bands);
                break;
            case "coupons":
                $coupons = Coupon::orderBy('id','DESC')->get();
                return ProductResource::collection($coupons);
                break;
            case "products":
                $products = Product::with('brand','product_info','attributes','category')->orderBy('id','DESC')->get();
                return ProductResource::collection($products);
                break;
            case "users":
                $users = User::orderBy('id','DESC')->get();
                return ProductResource::collection($users);
                break;
            case "category-aticles":
                $CateArticle = CateArticle::orderBy('id','DESC')->get();
                return ProductResource::collection($CateArticle);
                break;
            case "articles":
                $articles = Article::orderBy('id','DESC')->get();
                return ProductResource::collection($articles);
                break;
            default:
              echo "URL does not exist";
        }
    }

}
