<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_posts()
    {
        // Create a user and authenticate them
        $user = User::factory()->create();

        // Create posts (these can belong to any user, but you can also associate them with the authenticated user if needed)
        Post::factory()->count(15)->create([
            'status' => 'published'
        ]);

        // Make a GET request as the authenticated user
        $response = $this->actingAs($user)
            ->getJson('/api/posts');

        Log::info($response->getContent());
        // Assert that the response is successful and has the correct structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'body', 'status', 'user_id', 'created_at', 'updated_at', 'user', 'comments']
                ],
                'links'
            ])
            ->assertJsonCount(10, 'data'); // Assuming pagination defaults to 10 items per page

        Log::info($response->getContent());
    }

    public function test_can_search_posts()
    {
        // Create a user and authenticate them
        $user = User::factory()->create();

        // Create posts with specific titles
        Post::factory()->create(['title' => 'Test Post']);
        Post::factory()->create(['title' => 'Another Post']);

        // Add a log to confirm post creation
        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);

        // Make a GET request with a search query as the authenticated user
        $response = $this->actingAs($user)
            ->getJson('/api/posts?search=Test');

        $response->assertStatus(200);
    }

    public function test_can_filter_posts_by_status()
    {
        // Create a user and authenticate them
        $user = User::factory()->create();

        // Create posts with different statuses
        Post::factory()->create(['status' => 'published']);
        Post::factory()->create(['status' => 'draft']);

        // Make a GET request with a filter query for the status
        $response = $this->actingAs($user)
            ->getJson('/api/posts?status=published');

        // Assert that the response only includes the 'published' posts
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'published');
    }


    public function test_can_create_post()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'New Post',
                'body' => 'This is the body of the new post',
                'status' => 'published',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'New Post',
                'body' => 'This is the body of the new post',
                'status' => 'published',
                'user_id' => $user->id,
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'New Post',
            'body' => 'This is the body of the new post',
            'status' => 'published',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_update_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/posts/{$post->id}", [
                'title' => 'Updated Title',
                'body' => 'Updated body content',
                'status' => 'draft',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'title' => 'Updated Title',
                'body' => 'Updated body content',
                'status' => 'draft',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'body' => 'Updated body content',
            'status' => 'draft',
        ]);
    }

    public function test_can_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_cannot_update_post_of_another_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->putJson("/api/posts/{$post->id}", [
                'title' => 'Trying to update',
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_delete_post_of_another_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);
    }
}
