<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()->orderBy('created_at', 'DESC');

        return response()->inertiaOrJson('Example/Posts/Index', ['posts' => $posts->paginate()]);
    }

    public function create()
    {
        return Inertia::render('Example/Posts/Create');
    }

    public function show(Post $post)
    {
        return response()->inertiaOrJson('Example/Posts/Edit', ['post' => $post]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $post = Post::query()->create($request->all());

        return redirect()->route('posts.index');

        // if you want to redirect to the post
        // return redirect()->route('posts.show', [$post->id]);
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        $post->update($request->all());

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index');
    }
}
