<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //super admin
        User::factory()->create([
            'name' => 'Super Admin User',
            'email' => 'test@test.com',
        ]);

        User::factory(6)
            ->create()
            ->each(
                fn ($user) => Post::factory(4)->forUser($user)->create()
            );
    }
}
