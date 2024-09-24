<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_comments_for_post()
    {
        // Create a user and authenticate them
        $user = User::factory()->create();

        // Simulate the authenticated user
        $this->actingAs($user);

        // Create a post
        $post = Post::factory()->create();

        // Create comments associated with the post
        $comments = Comment::factory()->count(3)->create(['post_id' => $post->id]);

        // Make the request as the authenticated user
        $response = $this->getJson("/api/posts/{$post->id}/comments");

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'body', 'user_id', 'post_id', 'created_at', 'updated_at']
            ]);
    }
    public function test_can_create_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/comments", [
                'body' => 'This is a test comment',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'body' => 'This is a test comment',
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a test comment',
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_can_update_comment()
    {
        // A user can update their comment
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/posts/{$post->id}/comments/{$comment->id}", [
                'body' => 'This is an updated comment',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $comment->id,
                'body' => 'This is an updated comment',
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'This is an updated comment',
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_can_delete_comment() {
        // A user can delete their comment
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/posts/{$post->id}/comments/{$comment->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_cannot_update_comment_of_another_user() {
        // A user cannot update another user's comment
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/posts/{$post->id}/comments/{$comment->id}", [
                'body' => 'This is an updated comment',
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_delete_comment_of_another_user() {
        // A user cannot delete another user's comment
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/posts/{$post->id}/comments/{$comment->id}");

        $response->assertStatus(403);
    }
}
