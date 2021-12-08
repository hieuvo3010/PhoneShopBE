<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
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
        $Articles = Article::with('cate_article')->orderBy('id','DESC')->get();
        return ArticleResource::collection($Articles);
    }

    
    public function store(Request $request)
    {
        //
        $data = new Article();
        $data->fill($request->validate([
            'name' => 'required|unique:articles',
            'desc' => 'required',
            'image' => 'required',
            'cate_article_id' => 'required',
            'slug' => 'required|unique:articles',
        ]));
        $data->save();
        return response([
            'data' => new ArticleResource($data)
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
        $Article = Article::where('slug',$slug)->get();
        if(isset($Article)){
            return new ArticleResource($Article);
        }else{
            return response([
                'message' => 'This article does not exist'
            ], 400);
        }
    }

    public function update(Request $request)
    {
        //
        $slug = $request->query('slug');
        $Article = Article::where('slug',$slug)->first();
        if(isset($Article)){
            $Article->update($request->all());
            return response([
                'message' => 'Updated successfully',
                'data' => new ArticleResource($Article)
            ], 200);
        }else{
            return response([
                'message' => 'This article does not exist'
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        //
        $slug = $request->query('slug');
        $Article = Article::where('slug',$slug)->first();
        $result = $Article->destroy($Article->id);
        if($result){
            return response([
                'message' => 'Delete  Article successfully'
            ], 201);
        }
    }
}
