<?php

namespace App\Models\QueryBuilders;

use App\Enums\SearchEnum;
use Illuminate\Database\Eloquent\Builder;

class TagQueryBuilder extends Builder
{
    public function search(?string $term): self
    {
        return $this->when(
            $term,
            fn ($q, $term) => $q->where(
                fn ($q) => $q->where('name', 'like', "%$term%")
                    ->orWhere('description', 'like', "%$term%")
            )
        );
    }

    public function filter(array $filters): self
    {
        return $this->when(
            @$filters[SearchEnum::TAG_NAME],
            fn ($q, $name) => $q->where('name', $name)
        );
    }
}
