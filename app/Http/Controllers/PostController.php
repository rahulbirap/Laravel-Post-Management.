<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function home()
    {
        $posts = Post::orderBy('id', 'DESC')->simplePaginate(10);
        return view('dashboard', compact('posts'));
    }

    public function save_post(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $imageName = null; // Initialize $imageName to null
    
        if ($request->hasFile('image')) {
            // New image uploaded, process and save it
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
        } else if ($request->id && $existingPost = Post::find($request->id)) {
            // No new image uploaded but we are updating a post, use the existing image
            $imageName = $existingPost->image;
        }
    
        $postData = [
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imageName
        ];
    
        $response = Post::updateOrCreate(
            ['id' => $request->id],
            $postData
        );
    
        $html = '';
        $allPosts = Post::orderby('id', 'DESC')->get();
        foreach($allPosts as $post) {
            $html .= '<tr>';
            $html .= '<td>'.$post->id.'</td>';
            $html .= '<td>'.$post->title.'</td>';
            $html .= '<td>'.$post->content.'</td>';
            $html .= '<td><a href="#" id="edit_post" data-id="'.$post->id.'"><i class="fa fa-edit"></i></a><a href="#" id="delete_post" data-id="'.$post->id.'"><i class="fa fa-trash-o"></i></a></td>';
            $html .= '</tr>';
        }
    
        return response()->json([$html]);
    }
    
    
    public function get_post(Post $post){
        return response()->json($post);
    }
    
    public function delete_post(Request $request){
        $post = Post::find($request->id);
        $post->delete();
        return response()->json('Deleted Successfully');
    }
    
  
}
