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
}
