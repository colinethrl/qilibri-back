<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PostController extends Controller
{
    public function getPosts() {
        $posts = Post::where('published_at', '>=', date("Y-m-d"))
        ->with('user')
        ->orderBy('published_at', 'desc')
        ->get();

        return response()->json($posts);
    }

    public function createPost(Request $request) {
        $post = new Post;
        $post->title = $request->title;
        $post->body = $request->body;
        $post->user_id = 3;
        $post->published_at = $request->published_at ? Carbon::createFromTimestamp($request->published_at) : null;

        $post->save();
        return $post;
    }

    public function getUserPosts($user_id) {
        $posts = Post::where('user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($posts);
    }

    
}
