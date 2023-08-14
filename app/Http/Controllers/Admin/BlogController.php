<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\CentralLogics\Helpers;

class BlogController extends Controller
{
    public function list(Request $request)
    {

        $blogs          = Blog::where('is_active', 'Y')->paginate(25);
        $total          =$blogs->total();
        return view('admin-views.blog.list',[
            'blogs'=>$blogs,
            'total'=>$total,
        ]);
    }
    public function edit(Request $request, $id)
    {
        $blog           = Blog::where('id', $id)->first();
        return view('admin-views.blog.edit', compact('blog'));
    }
    public function create(Request $request)
    {
        return view('admin-views.blog.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'post_title'=>'required',
            'post_slug'=>'required'
        ]);

        $blog                       = new Blog();
        $blog->time_id              = date('U');
        $blog->post_title           = $request->post_title;
        $blog->slug                 = $request->post_slug;
        $blog->type                 = $request->type;
        $blog->meta_title           = $request->meta_title;
        $blog->meta_tags            = $request->meta_tags;
        $blog->meta_description     = $request->meta_description;
        $blog->content              = $request->content;
        $blog->recipe_incrediants   = $request->recipe_incrediants;
        if($request->hasFile('post_image')){

            $blog->image                = Helpers::upload('admin_feature/', 'png', $request->file('post_image'));
        }
        $blog->save();
        Toastr::success('Post has been added successfully');
        return back();

    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'post_title'=>'required',
            'post_slug'=>'required'
        ]);

        $blog                       = Blog::find($id);
        $blog->post_title           = $request->post_title;
        $blog->slug                 = $request->post_slug;
        $blog->type                 = $request->type;
        $blog->meta_title           = $request->meta_title;
        $blog->meta_tags            = $request->meta_tags;
        $blog->meta_description     = $request->meta_description;
        $blog->content              = $request->content;
        $blog->recipe_incrediants   = $request->recipe_incrediants;
        if($request->hasFile('post_image')){
            $blog->image                = Helpers::upload('admin_feature/', 'png', $request->file('post_image'));
        }
        $blog->save();
        Toastr::success('Post has been updated successfully');
        return back();

    }
}
