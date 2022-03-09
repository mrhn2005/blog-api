<?php

namespace App\Http\Controllers\Api;

use App\Actions\TagAction;
use App\Enums\SearchEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TagCreateRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = Tag::query()
            ->search($request->input(SearchEnum::SEARCH_TERM))
            ->filter($request->query())
            ->paginate($request->input(SearchEnum::PER_PAGE) ?: 15);

        return TagResource::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TagCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagCreateRequest $request, TagAction $tagAction)
    {
        $imagePath = $tagAction->uploadPhoto($request->file('image'));

        $tag = Tag::create(
            array_merge($request->validated(), ['image' => $imagePath])
        );

        return TagResource::make($tag)
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
        $tag = Tag::findOrFail($id);

        return TagResource::make($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\TagCreateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TagCreateRequest $request, Tag $tag, TagAction $tagAction)
    {
        $imageArray = [];
        if ($request->has('image')) {
            $tagAction->deletePhotos($tag);
            $imageArray['image'] = $tagAction->uploadPhoto($request->file('image'));
        }

        $tag->update(array_merge($request->validated(), $imageArray));

        return TagResource::make($tag)
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
        //
    }
}
