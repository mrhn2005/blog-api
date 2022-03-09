<?php

namespace App\Models\QueryBuilders;

use App\Enums\SearchEnum;
use Illuminate\Database\Eloquent\Builder;

class PostQueryBuilder extends Builder
{
    public function search(?string $term): self
    {
        return $this->when(
            $term,
            fn ($q, $term) => $q->where(
                fn ($q) => $q->where('title', 'like', "%$term%")
                    // ->orWhereFullText('content', $term)
            )
        );
    }

    public function filter(array $filters): self
    {
        return $this
            ->when(
                @$filters[SearchEnum::USER_ID],
                fn ($q, $userId) => $q->where('user_id', $userId)
            )
            ->when(
                @$filters[SearchEnum::TAG_ID],
                function ($query, $tagIds) {
                    $query->withTags(
                        explode(SearchEnum::SEPARATOR, $tagIds)
                    );
                }
            );
    }

    public function sort(?string $sort): self
    {
        if (! $sort) {
            return $this->latest('id');
        }

        $sortDirection = $sort[0] === '-' ? 'desc' : 'asc';
        $sort = str_replace(['+', '-'], '', $sort);

        return $this
            ->when(
                $sort === 'created_at',
                fn ($q) => $q->orderBy('created_at', $sortDirection)
            )
            ->when(
                $sort === 'title',
                fn ($q) => $q->orderBy('title', $sortDirection)
            );
    }
}
