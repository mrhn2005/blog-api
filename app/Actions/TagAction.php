<?php

namespace App\Actions;

use App\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TagAction
{
    public function uploadPhoto(UploadedFile $file): string
    {
        return $file->store($this->imageLocationFormat());
    }

    public function deletePhotos(Tag $tag): void
    {
        if (!$tag->image) {
            return;
        }

        Storage::delete($tag->image);
    }

    public function imageLocationFormat(): string
    {
        return 'tags/' . now()->format('Y-m-d');
    }
}
