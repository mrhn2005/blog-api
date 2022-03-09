<?php

namespace App\Models;

use App\Models\Collections\UserCollection;
use App\Models\QueryBuilders\UserQueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'birthday',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
    ];

    public function newEloquentBuilder($query): UserQueryBuilder
    {
        return new UserQueryBuilder($query);
    }

    public function newCollection(array $models = []): Collection
    {
        return new UserCollection($models);
    }

    //Relations
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    //Methods
    public function isAdmin(): bool
    {
        if ($this->id % 2) {
            return false;
        }

        return true;
    }

    public function isSuperAdmin(): bool
    {
        $superAdminEmails = explode(',', str_replace(' ', '', settings('super_admin_emails')));
        if (in_array($this->email, $superAdminEmails, true)) {
            return true;
        }

        return false;
    }
}
