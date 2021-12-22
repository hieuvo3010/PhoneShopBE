<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CateArticle;
use App\Http\Resources\CateArticleResource;
class CateArticleController extends Controller
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
        $CategoryArticles = CateArticle::orderBy('id','DESC')->where('status', 1)->get();
        return CateArticleResource::collection($CategoryArticles);
    }

    
    public function store(Request $request)
    {
        //
        $data = new CateArticle();
        $data->fill($request->validate([
            'name' => 'required|max:255|unique:cate_articles',
            'desc' => 'required',
            'slug' => 'required|unique:cate_articles',
        ]));
        $data->save();
        return response([
            'data' => new CateArticleResource($data)
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
        //
        $slug = $request->query('slug');
        $CateArticle = CateArticle::where('slug',$slug)->get();
        if(isset($CateArticle)){
            return new CateArticleResource($CateArticle);
        }else{
            return response([
                'message' => 'This brand does not exist'
            ], 400);
        }
    }

    public function update(Request $request)
    {
        //
        $slug = $request->query('slug');
        $CateArticle = CateArticle::where('slug',$slug)->first();
        if(isset($CateArticle)){
            $CateArticle->update($request->all());
            return response([
                'message' => 'Updated successfully',
                'data' => new CateArticleResource($CateArticle)
            ], 200);
        }else{
            return response([
                'message' => 'This category article does not exist'
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        //
        $slug = $request->query('slug');
        $CateArticle = CateArticle::where('slug',$slug)->first();
        $result = $CateArticle->destroy($CateArticle->id);
        if($result){
            return response([
                'message' => 'Delete Cate Article successfully'
            ], 201);
        }
    }
}
