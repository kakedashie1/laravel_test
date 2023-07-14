<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()->onlyOpen()->with('user')->withCount('comments')->orderByDesc('comments_count')->get();
        return view('index', compact('posts'));

    }

    public function show(Post $post)
    {
        if($post->isClosed()) {
            return abort(403);
        }

        return view('posts.show', compact('post'));
    }
}
