<?php

namespace Tests\Feature\Controllers;

use App\Actions\PostAction;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_posts()
    {
        $user = User::factory()->create();
        $posts = Post::factory()->forUser($user)->count(4)->create();
        $response = $this->actingAs($user)->getJson('api/posts');

        $response->assertSuccessful();

        //clean-up
        $posts->each(fn ($post) => app(PostAction::class)->deletePhotos($post));
    }
}
