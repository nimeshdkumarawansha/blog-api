<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::query()->with('user', 'comments.user');

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }else{
            $query->where('status', 'published');
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'status' => 'required|in:published,draft',
        ]);

        $post = $request->user()->posts()->create($request->all());

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->all());

        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
}
