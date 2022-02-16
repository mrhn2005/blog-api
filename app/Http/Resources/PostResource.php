<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image_links,
            'can_user_manage' => auth()->user()->can('update', $this),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'author' => UserResource::make($this->whenLoaded('author')),
        ];
    }
}
