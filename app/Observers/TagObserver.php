<?php

namespace App\Observers;

use App\Actions\TagAction;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;

class TagObserver
{
    public function created(Tag $tag): void
    {
        //
    }

    public function updated(Tag $tag): void
    {
        //
    }

    public function deleted(Tag $tag): void
    {
        app(TagAction::class)->deletePhotos($tag);
    }
}
