<?php

namespace Tests\Feature\Controllers;

use App\Actions\PostAction;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostTagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_attach_tags_to_post()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $post = Post::factory()->create();
        $inputs = [
            'tags' => [
                $tag->id,
            ]
        ];

        $response = $this->actingAs($user)
            ->putJson('api/posts/'.$post->id.'/tags/attach', $inputs);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($post->tags->first()->id, $tag->id);
        $this->assertCount(1, $post->tags);

        //clean-up
        app(PostAction::class)->deletePhotos($post);
    }

    public function test_user_can_detach_tags_from_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->hasTags(2)->create();
        $inputs = [
            'tags' => $post->tags()->pluck('id'),
        ];

        $response = $this->actingAs($user)
            ->putJson('api/posts/'.$post->id.'/tags/detach', $inputs);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(0, $post->tags);

        //clean-up
        app(PostAction::class)->deletePhotos($post);
    }
}
