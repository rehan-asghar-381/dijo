<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use DB;

class BlogController extends Controller
{

    public function blogs(Request $request)
    {
        $stories        = Blog::where("type", "Story")->get();
        $recipies       = Blog::where("type", "Recipe")->get();
        return view('blogs', compact('stories', 'recipies'));
    }
    public function post_detail(Request $request, $slug)
    {

        $slug           = explode('-', $slug);
        $slug           = end( $slug);
        $blog           = Blog::find($slug);
        $recipies       = Blog::where("type", "Recipe")->get();
        return view('blog-post', compact('blog', 'recipies'));
    }






}