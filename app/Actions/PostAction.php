<?php

namespace App\Actions;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostAction
{
    public function uploadPhoto(UploadedFile $file): string
    {
        return $file->store($this->imageLocationFormat());
    }

    public function deletePhotos(Post $post): void
    {
        Storage::delete([$post->image, $post->thumbnail_image]);
    }

    public function imageLocationFormat(): string
    {
        return 'posts/' . now()->format('Y-m-d');
    }
}
