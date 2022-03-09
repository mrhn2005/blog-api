<?php

namespace App\Models\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    public static function bootHasSlug()
    {
        static::deleted(function (Model $model) {
            $tags = $model->tags()->get();

            $model->detachTags($tags);
        });
    }

    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(Tag::class, 'taggable')
            ->latest('tag_id');
    }

    public function attachTags(array $tags): static
    {
        $this->tags()->syncWithoutDetaching($tags);

        return $this;
    }

    public function detachTags(array $tags): static
    {
        $tags = Tag::find($tags);

        collect($tags)
            ->filter()
            ->each(fn (Tag $tag) => $this->tags()->detach($tag));

        return $this;
    }
}
