<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $users = User::factory(15)->create();
    $first = $users->first();

    $users->each(function ($user) {
        Post::factory(random_int(2,5))->create(['user_id' => $user->id])->each(function ($post) {
            Comment::factory(random_int(1, 5))->create(['post_id' => $post->id]);
        });
    });

    $first->update([
        'name' => 'シロ',
        'email' => 'aaa@bbb.net',
        'password' => Hash::make('hogehoge'),
    ]);
}


}
