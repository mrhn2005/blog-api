<?php

namespace App\Http\Controllers;

use App\Actions\PostAction;
use App\Enums\SearchEnum;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = Post::query()
            ->search($request->input(SearchEnum::SEARCH_TERM))
            ->filter($request->query())
            ->sort($request->input(SearchEnum::SORT))
            ->with(['author'])
            ->paginate($request->input(SearchEnum::PER_PAGE) ?: 15)
            ->appends($request->query());

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostCreateRequest $request, PostAction $postAction)
    {
        $this->authorize('create', Post::class);

        $imagePath = $postAction->uploadPhoto($request->file('image'));

        $post = auth()->user()->posts()->create(
            array_merge($request->validated(), ['image' => $imagePath])
        );

        return PostResource::make($post)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with(['author'])->findOrFail($id);

        return PostResource::make($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PostUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostUpdateRequest $request, Post $post, PostAction $postAction)
    {
        $this->authorize('update', $post);

        $imageArray = [];
        if ($request->has('image')) {
            $postAction->deletePhotos($post);
            $imageArray['image'] = $postAction->uploadPhoto($request->file('image'));
        }

        $post->update(array_merge($request->validated(), $imageArray));

        return PostResource::make($post)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('delete', $post);

        $post->delete();

        return response()
            ->json()
            ->setStatusCode(Response::HTTP_OK);
    }
}
