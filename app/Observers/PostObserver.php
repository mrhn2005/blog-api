<?php

namespace App\Observers;

use App\Actions\PostAction;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostObserver
{
    public function created(Post $post): void
    {
        if ($post->image) {
            makeThumbnail(Storage::path($post->image), 100, 100);
        }
    }

    public function updated(Post $post): void
    {
        if ($post->isDirty('image') && $post->image) {
            makeThumbnail(Storage::path($post->image), 100, 100);
        }
    }

    public function deleted(Post $post): void
    {
        app(PostAction::class)->deletePhotos($post);
    }
}
