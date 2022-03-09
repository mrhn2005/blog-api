<?php

namespace App\Models;

use App\Models\QueryBuilders\PostQueryBuilder;
use App\Models\Traits\HasTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory,
        HasTags;

    protected $fillable = [
        'title',
        'content',
        'image',
    ];

    public function newEloquentBuilder($query): PostQueryBuilder
    {
        return new PostQueryBuilder($query);
    }

    //Relations
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //Accessors
    public function getExcerptAttribute()
    {
        return Str::limit(strip_tags($this->content), 100, '...');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        return Storage::url($this->image);
    }

    public function getImageFullUrlAttribute()
    {
        if (!$this->image || !Storage::exists($this->image)) {
            return 'https://picsum.photos/400/400';
        }

        return asset(Storage::url($this->image));
    }

    public function getThumbnailImageAttribute()
    {
        if (!$this->image) {
            return null;
        }

        $prod['item_image_url'] = 'new-thumb-01.jpg';

        $fileparts = pathinfo($this->image);

        if (!$fileparts) {
            return null;
        }

        return @$fileparts['dirname'] . '/' . @$fileparts['filename'] . '_100x100.' . @$fileparts['extension'];
    }

    public function getThumbnailImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        return Storage::url($this->thumbnail_image);
    }

    public function getImageLinksAttribute()
    {
        return [
            'original' => $this->image_full_url,
            'thumbnail' => asset($this->thumbnail_image_url),
        ];
    }
}
