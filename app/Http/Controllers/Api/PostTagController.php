<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostTagRequest;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

class PostTagController extends Controller
{
    public function attach(PostTagRequest $request, Post $post)
    {
        $post->attachTags($request->tags);

        return response()
            ->json()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function detach(PostTagRequest $request, Post $post)
    {
        $post->detachTags($request->tags);

        return response()
            ->json()
            ->setStatusCode(Response::HTTP_OK);
    }
}
