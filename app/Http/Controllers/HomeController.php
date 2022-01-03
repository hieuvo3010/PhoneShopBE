<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HomeResource;
use App\Product, App\Attribute, App\Brand, App\Article, App\CateArticle, App\Order;
use WithPagination, DB;
use Illuminate\Pagination\LengthAwarePaginator;
class HomeController extends Controller
{
    //
    public function show_product(Request $request){ 
        $this->pagesize = 10;
        if($request->query() == null ){
            $s = Product::with('brand')->where('status',1);
            $products = $s->get();
    
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
 
        // Create a new Laravel collection from the array data
        $itemCollection = collect($products);
 
        // Define how many items we want to be visible in each page
        $perPage = 10;
 
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();
 
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
 
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        return response([
            'message' => 'Success filter products',
            'data' => $paginatedItems
         
        ], 200);
        }
        $s = Product::with('brand');
     
        if(isset($_GET['min_price']) && isset($_GET['max_price']) && isset($_GET['brand_id'])) { // checkbox price && brand
            $min_price = $_GET['min_price'];
            $max_price = $_GET['max_price'];
                foreach($_GET['brand_id'] as $value) {
                    $s->orWhere('price','>=',$min_price)
                    ->where('price','<=',$max_price)
                    ->where('brand_id', $value)->where('status',1);

                }
        }else{
            if(isset($_GET['min_price']) && isset($_GET['max_price'])) { // check price
                $min_price = $_GET['min_price'];
                $max_price = $_GET['max_price'];
                $s->where('status',1)->whereBetween('price',[$min_price,$max_price]);
            }
            
            if(isset($_GET['brand_id'])){ // check brand
                    foreach($_GET['brand_id'] as $value) {
                        $s->orWhereIn('brand_id', [$value])->where('status',1);
                    }
            }
        }
        

        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
            if($sort_by == 'discount'){
                $s->orderBy('discount','DESC');
            }elseif($sort_by == 'desc'){
                $s->orderBy('price','DESC');
            }elseif($sort_by == 'asc'){
                $s->orderBy('price','ASC');
            }
        }
        $products = $s->get();
    
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
 
        // Create a new Laravel collection from the array data
        $itemCollection = collect($products);
 
        // Define how many items we want to be visible in each page
        $perPage = 10;
 
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();
 
        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
 
        // set url path for generted links
        $paginatedItems->setPath($request->url());

        return response([
            'message' => 'Success filter products',
            'data' => $paginatedItems
         
        ], 201);
    }




    public function show_color_products(Request $request){
        $colors = Attribute::all();
        return response([
            'message' => 'All colors product',
            'data' => 
               HomeResource::collection($colors),
        ], 201);
    }

    public function getSearchResults(Request $request) {
        $search_product = Product::all();
    
        $data = $request->query('data');

        $drivers = Product::with('brand','product_info','attributes','ratings')
                        ->where('status', 1)
                        ->where(function($query) use ($data){
                            $query->where('name','like',"%{$data}%")
                           ->orWhere('slug','like',"%{$data}%");
                       })
                        ->paginate(10);
        if($drivers){
            return response([
                'data' => $drivers
            ]);
        }
        return response([
            'data' => 'None product'
        ]);
    }

    public function get_articles_by_cate(Request $request){
        try{
            $data = $request->query('slug');
            $cateArticle = CateArticle::where('slug',$data)->where('status', 1)->first();
            $articles = Article::where('cate_article_id',$cateArticle->id)->get();
            return response([
                'data' => HomeResource::collection($articles),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => __('Category Article slug does not exist'),
            ], 400);
        }
    }

    public function check_order(Request $request){
        $data1 = $request->query('phone');
        $data2 = $request->query('order_code');
        // $o = Order::with('ship')->where('ship.phone',$data1)->where('order_code',$data2)->first();

        $query = DB::table('orders')
                ->join('ships', 'orders.ship_id', '=', 'ships.id')
                ->where('orders.order_code',  $data2)
                ->where('ships.phone', $data1)
                ->select('orders.*', 'ships.*')
                ->get();
        return response()->json([
            'data' => $query
        ], 200);
    }
}
