<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        // @test
        $post = Post::factory()->create();

        $this->assertInstanceOf(User::class, $post->user);
    }

    public function test_comments()
    {
        $post = Post::factory()->create();

        $this->assertInstanceOf(Collection::class, $post->comments);
    }

    public function test_scope()
    {
        $post1 = Post::factory()->closed()->create();

        $post2 = Post::factory()->create();

        $posts = Post::onlyOpen()->get();

        $this->assertFalse($posts->contains($post1));
        $this->assertTrue($posts->contains($post2));
    }

    public function test_isClosed()
    {
        $open = Post::factory()->create();
        $closed = Post::factory()->closed()->create();

        $this->assertFalse($open->isClosed());
        $this->assertTrue($closed->isClosed());
    }
}
