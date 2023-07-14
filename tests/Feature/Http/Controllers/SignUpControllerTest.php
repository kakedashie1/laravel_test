<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SignUpControllerTest extends TestCase
{
    use RefreshDatabase;

  public function test_user_index()
  {
    $this->get('signup')
         ->assertOk();
  }

  public function test_touroku()
  {
    $validData = [
        'name' => '太郎',
        'email' => 'test@example.com',
        'password' => 'hogehoge',
    ];

    // $validData = User::factory()->validData();



    $this->post('signup', $validData)
         ->assertRedirect('mypage/posts');

    unset($validData['password']);

    $this->assertDatabaseHas('users', $validData);

    $user = User::firstWhere($validData);

    $this->assertTrue(Hash::check('hogehoge', $user->password));

    $this->assertAuthenticatedAs($user);
  }

  public function test_not_user()
  {
    $url = 'signup';

    $this->post($url,[])
         ->assertRedirect();

    User::factory()->create(['email' => 'aaa@bbb.net']);

    $this->post($url, ['name' => ''])->assertInvalid(['name' => '指定']);
    $this->post($url, ['name' => str_repeat('あ',21)])->assertInvalid(['name' => '指定']);
    $this->post($url, ['name' => str_repeat('あ',20)])->assertvalid('name');

    $this->post($url, ['email' => ''])->assertInvalid(['email' => '指定']);
    $this->post($url, ['email' => 'aa@bb@cc'])->assertInvalid(['email' => '指定']);
    $this->post($url, ['email' => 'aa@bb@ccああ'])->assertInvalid(['email' => '指定']);
    $this->post($url, ['email' => 'aaa@bbb.net'])->assertInvalid(['email' => '存在']);

    $this->post($url, ['password' => ''])->assertInvalid(['password' => '指定']);
    $this->post($url, ['password' => 'abcd12'])->assertInvalid(['password' => '指定']);
    $this->post($url, ['password' => 'abcd1234'])->assertvalid('password');

  }
}
