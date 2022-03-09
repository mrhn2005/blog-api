<?php

namespace Tests\Feature\Controllers;

use App\Actions\PostAction;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cant_see_posts()
    {
        $response = $this->getJson('api/posts');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_can_fetch_posts()
    {
        $user = User::factory()->create();
        $posts = Post::factory()->forUser($user)->count($count = 4)->create();
        $response = $this->actingAs($user)->getJson('api/posts');

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('meta.total', $count)
                    ->has('data.0.title')
                    ->etc()
            );

        //clean-up
        $posts->each(fn ($post) => app(PostAction::class)->deletePhotos($post));
    }

    public function test_admin_user_can_create_post()
    {
        $user = User::factory(2)->create()->admins()->first();
        $tag = Tag::factory()->create();
        $inputs = [
            'title' => 'test',
            'content' => '<p>Test Content</p>',
            'image' => UploadedFile::fake()->image('avatar.jpg', 500, 500),
            'tags' => [
                $tag->id,
            ]
        ];

        $response = $this->actingAs($user)
            ->postJson('api/posts', $inputs);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('posts', Arr::only($inputs, ['title', 'content']));

        $post = $user->posts()->first();
        $this->assertEquals($post->tags->first()->id, $tag->id);

        $this->assertFileExists(Storage::path($post->image));

        //clean-up
        app(PostAction::class)->deletePhotos($post);
    }

    public function test_admin_user_can_update_post()
    {
        $user = User::factory(2)->create()->admins()->first();
        $post = Post::factory()->forUser($user)->create();
        $inputs = [
            'title' => 'test',
            'content' => '<p>Test Content</p>',
        ];

        $response = $this->actingAs($user)
            ->putJson('api/posts/'.$post->id, $inputs);

        $response->assertStatus(Response::HTTP_OK);

        $post->refresh();
        $this->assertEquals($inputs, $post->only('title', 'content'));

        //clean-up
        app(PostAction::class)->deletePhotos($post);
    }

    public function test_admin_user_can_delete_own_post()
    {
        $user = User::factory(2)->create()->admins()->first();
        $post = Post::factory()->forUser($user)->create();
        $response = $this->actingAs($user)->deleteJson('api/posts/'.$post->id);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }
}
