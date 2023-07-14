<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostManageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_open_mypage()
    {
        $loginUrl = 'mypage/login';
        $this->get('mypage/posts')->assertRedirect($loginUrl);
        $this->get('mypage/posts/create')->assertRedirect($loginUrl);
        $this->get('mypage/posts/create', [])->assertRedirect($loginUrl);
        $this->get('mypage/posts/edit/1')->assertRedirect($loginUrl);
        $this->post('mypage/posts/edit/1', [])->assertRedirect($loginUrl);
        $this->delete('mypage/posts/delete/1')->assertRedirect($loginUrl);
    }

    public function test_open_only_mypage()
    {
       $user = $this->login();

       $other = Post::factory()->create();
       $mypost = Post::factory()->create(['user_id' => $user->id]);

       $this->get('mypage/posts')
            ->assertOk()
            ->assertDontSee($other->title)
            ->assertSee($mypost->title);
    }

    public function test_posts_open_create()
    {
        $this->login();
        $this->get('mypage/posts/create')
             ->assertOk();
    }

    public function test_posts_create_open()
    {
        $this->withExceptionHandling();

        [$taro, $me, $jiro] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body'  => '私のブログ本文',
            'status' => '1'
        ];

        $response = $this->post('mypage/posts/create', $validData);

        $post = Post::first();

        $response->assertRedirect('mypage/posts/edit' .$post->id);

        $this->assertDatabaseHas('posts', array_merge($validData, ['user_id' => $me->id]));
    }

    public function test_posts_create_close()
    {
        $this->withExceptionHandling();

        [$taro, $me, $jiro] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body'  => '私のブログ本文',
            // 'status' => '1'
        ];

        $this->post('mypage/posts/create', $validData);

        $this->assertDatabaseHas('posts', array_merge($validData, [

            'user_id' => $me->id,
            'status' => 0,
        ]));
    }

    public function test_check()
    {
        $url = 'mypage/posts/create';

        $this->login();

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        app()->setLocale('testing');

        $this->post($url, ['title' => ''])->assertInvalid(['title' => 'required']);
        $this->post($url, ['title' => str_repeat('a', 256)])->assertInvalid(['title' => 'title']);
        $this->post($url, ['title' => str_repeat('a', 255)])->assertValid('title');
        $this->post($url, ['body' => ''])->assertInvalid(['body' => 'required']);

    }

    public function test_can_open_myEdit()
    {
        $post = Post::factory()->create();

        $this->login($post->user);
        $this->get('mypage/posts/edit/' .$post->id)
             ->assertOk();

    }
    public function test_cant_open_othersEdit()
    {
        $post = Post::factory()->create();

        $this->login();
        $this->get('mypage/posts/edit/' .$post->id)
             ->assertForbidden();

    }

    public function test_can_update_myBlogs()
    {
        $validData = [
            'title' => '新タイトル',
            'body'  => '新本文',
            'status' => '1'
        ];

        $post = Post::factory()->create();

        $this->login($post->user);

        $this->post('mypage/posts/edit/'.$post->id, $validData)
             ->assertRedirect('mypage/posts/edit/' .$post->id);

        $this->get('mypage/posts/edit/'.$post->id)
             ->assertSee('ブログを更新しました');

        $this->assertDatabaseHas('posts', $validData);
        $this->assertCount(1, Post::all());

        $post->refresh();

        $this->assertSame('新タイトル', $post->title);
        $this->assertSame('新本文', $post->body);
    }
    public function test_cant_update_othersBlogs()
    {
        $validData = [
            'title' => '新タイトル',
            'body'  => '新本文',
            'status' => '1'
        ];

        $post = Post::factory()->create(['title' => '元のタイトル']);

        $this->login();

        $this->post('mypage/posts/edit/'.$post->id, $validData)
             ->assertForbidden();

        $this->assertSame('元のタイトル', $post->fresh()->title);

    }
    public function test_can_delete_MyBlogs()
    {
        $post = Post::factory()->create();

        $myPostComment = Comment::factory()->create(['post_id' => $post->id]);
        $otherPostComment = Comment::factory()->create();

        $this->login($post->user);

        $this->delete('mypage/posts/delete/'.$post->id)
             ->assertRedirect('mypage/posts');

        $this->assertModelMissing($post);

        $this->assertModelMissing($myPostComment);
        $this->assertModelExists($otherPostComment);


    }
    public function test_cant_delete_othersBlogs()
    {
        $post = Post::factory()->create();


        $this->login();

        $this->delete('mypage/posts/delete/'.$post->id)
             ->assertForbidden();
         $this->assertModelExists($post);

    }

}
