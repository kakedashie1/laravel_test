<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserLoginControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_login(): void
    {
        $this->get('mypage/login')
             ->assertOK();
    }

    public function test_check()
    {
        $url = 'mypage/login';

    $this->post($url,[])
         ->assertRedirect();

    // User::factory()->create(['email' => 'aaa@bbb.net']);


    $this->post($url, ['email' => ''])->assertInvalid(['email' => '指定']);
    $this->post($url, ['email' => 'aa@bb@cc'])->assertInvalid(['email' => '指定']);
    $this->post($url, ['email' => 'aa@bb@ccああ'])->assertInvalid(['email' => '指定']);

    $this->post($url, ['password' => ''])->assertInvalid(['password' => '指定']);

    }

    public function test_can_login()
    {
        $user = User::factory()->create([
            'email' => 'aaa@bbb.net',
            'password' => (Hash::make('abcd1234')),
        ]);

        $this->post('mypage/login', [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
         ])->assertRedirect('mypage/posts');

        $this->assertAuthenticatedAs($user);
    }

    public function test_fail_login()
    {
        $url = 'mypage/login';
        $user = User::factory()->create([
            'email' => 'aaa@bbb.net',
            'password' => (Hash::make('abcd1234')),
        ]);

        $this->from($url)->post('mypage/login', [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd123455',
         ])->assertRedirect($url);

         $this->get($url)
              ->assertOk()
              ->assertSee('メールアドレスかパスワードが間違っています');

    }

    public function test_logout()
    {
        $this->login();

        $this->post('mypage/logout')
             ->assertRedirect('mypage/login');

        $this->get('mypage/login')
             ->assertSee('ログアウトしました');

        $this->assertGuest();
    }
}
