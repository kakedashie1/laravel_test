<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Carbon;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {


        $post1 = Post::factory()->hasComments(3)->create();
        $post2 = Post::factory()->hasComments(5)->create();
        Post::factory()->hasComments(1)->create();


        $this->get('/')
        ->assertStatus(200)
        ->assertSee($post1->title)
        ->assertSee($post2->title)
        ->assertSee($post1->user->name)
        ->assertSee($post2->user->name)
        ->assertSee('(3件のコメント)')
        ->assertSee('(5件のコメント)')
        ->assertSeeInOrder([
            '(5件のコメント)',
            '(3件のコメント)',
            '(1件のコメント)',
        ]);

    }

    public function test_index()
    {
        $post1 = Post::factory()->closed()->create([
            'title' => 'これは非公開のブログです',
        ]);

        $post2 = Post::factory()->create([
            'title' => 'これは公開済みのブログです',
        ]);

        $this->get('/')->assertDontSee('これは非公開のブログです')->assertSee('これは公開済みのブログです');
    }


    public function test_show()
    {
        $post = Post::factory()->create();

        Comment::factory()->createMany([
            ['created_at' => now()->sub('2 days'), 'name' => 'コメント太郎', 'post_id' => $post->id],
            ['created_at' => now()->sub('3 days'), 'name' => 'コメント次郎', 'post_id' => $post->id],
            ['created_at' => now()->sub('1 days'), 'name' => 'コメント三郎', 'post_id' => $post->id],

        ]);

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->user->name)
            ->assertSeeInOrder(['コメント次郎','コメント太郎','コメント三郎']);

    }

    public function test_not_show()
    {
        $post = Post::factory()->closed()->create();

        $this->get('posts/'.$post->id)
             ->assertForbidden();

    }

    public function test_chrismas()
    {
        $post = Post::factory()->create();

        Carbon::setTestNow('2020-12-24');

        $this->get('posts/'.$post->id)
             ->assertOk()
             ->assertDontSee('メリークリスマス！');


        Carbon::setTestNow('2020-12-25');

        $this->get('posts/'.$post->id)
             ->assertOk()
             ->assertSee('メリークリスマス！');
    }

    public function test_factory()
    {
        // $post = Post::factory()->create();
        // dump($post->toArray());

        // dump(User::get()->toArray());

        $this->assertTrue(true);
    }
}
