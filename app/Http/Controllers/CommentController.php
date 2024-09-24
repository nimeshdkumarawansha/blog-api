<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()->with('user')->get();

        return response()->json($comments);
    }
    public function store(Request $request, Post $post)
    {
        $request->validate(['body' => 'required']);

        $comment = $post->comments()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($comment, 201);
    }

    public function update(Request $request, $postId, $commentId)
    {
        $comment = Comment::where('post_id', $postId)->findOrFail($commentId);
        $this->authorize('update', $comment);

        $comment->update($request->all());

        return response()->json($comment);
    }


    public function destroy($postId, $commentId)
    {
        $comment = Comment::where('post_id', $postId)->findOrFail($commentId);

        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }
}