<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\HomeResource;
use App\Product, App\Attribute, App\Brand;
use WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
class HomeController extends Controller
{
    //
    public function show_product(Request $request){ 
        $this->pagesize = 10;
        $s = Product::with('brand');
     
        if(isset($_GET['min_price']) && isset($_GET['max_price']) && isset($_GET['brand_id'])) { // checkbox price && brand
            $min_price = $_GET['min_price'];
            $max_price = $_GET['max_price'];
                foreach($_GET['brand_id'] as $value) {
                    $s->orWhere('price','>=',$min_price)
                    ->where('price','<=',$max_price)
                    ->where('brand_id', $value);
                }
        }else{
            if(isset($_GET['min_price']) && isset($_GET['max_price'])) { // check price
                $min_price = $_GET['min_price'];
                $max_price = $_GET['max_price'];
                $s->whereBetween('price',[$min_price,$max_price]);
            }
            
            if(isset($_GET['brand_id'])){ // check brand
                    foreach($_GET['brand_id'] as $value) {
                        $s->orWhereIn('brand_id', [$value]);
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
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
 
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

        $drivers = Product::with('brand','product_info','attributes','ratings')->where('name', 'like', "%{$data}%")
                        ->orWhere('slug', 'like', "%{$data}%")
                        ->get();
        if($drivers){
            return response([
                'data' => $drivers
            ]);
        }
        return response([
            'data' => 'None product'
        ]);
    }

}
