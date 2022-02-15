<?php

namespace App\Models\Collections;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserCollection extends Collection
{
    public function admins(): self
    {
        return $this->filter(
            fn (User $user) => $user->isAdmin()
        );
    }
}
