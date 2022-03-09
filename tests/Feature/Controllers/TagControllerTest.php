<?php

namespace Tests\Feature\Controllers;

use App\Actions\TagAction;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_tags()
    {
        $user = User::factory()->create();
        Tag::factory()->count($count = 4)->create();
        $response = $this->actingAs($user)->getJson('api/tags');

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('meta.total', $count)
                    ->has('data.0.name')
                    ->etc()
            );
    }

    public function test_user_can_create_tag()
    {
        $user = User::factory()->create();

        $inputs = [
            'name' => 'test',
            'description' => 'test description',
            'image' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
        ];

        $response = $this->actingAs($user)
            ->postJson('api/tags', $inputs);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('tags', Arr::except($inputs, 'image'));

        $tag = Tag::latest('tag_id')->first();
        $this->assertFileExists(Storage::path($tag->image));

        //clean-up
        app(TagAction::class)->deletePhotos($tag);
    }

    public function test_user_can_view_tags()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $response = $this->actingAs($user)->getJson('api/tags/' . $tag->id);

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.name', $tag->name)
                    ->where('data.id', $tag->id)
                    ->etc()
            );
    }
}
