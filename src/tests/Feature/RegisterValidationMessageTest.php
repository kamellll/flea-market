<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterValidationMessageTest extends TestCase
{
    use RefreshDatabase;
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    /** @test */
    public function 名前未入力だとエラーメッセージが表示される()
    {
        $response = $this->from('/register')
            ->post('/register', $this->validPayload(['name' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name']);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSeeText('お名前を入力してください');
    }
    /** @test */
    public function email_required()
    {
        $response = $this->from('/register')
            ->post('/register', $this->validPayload(['email' => '']));

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSeeText('メールアドレスを入力してください');
    }
    /** @test */
    public function password_required()
    {
        $response = $this->from('/register')
            ->post('/register', $this->validPayload([
                'password' => '',
                'password_confirmation' => '',
            ]));

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSeeText('パスワードを入力してください');
    }
    /** @test */
    public function password_min()
    {
        $response = $this->from('/register')
            ->post('/register', $this->validPayload([
                'password' => '1234567', // 7文字
                'password_confirmation' => '1234567',
            ]));

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSeeText('パスワードは8文字以上で入力してください');
    }
    /** @test */
    public function password_confirmation_must_match()
    {
        $response = $this->from('/register')
            ->post('/register', $this->validPayload([
                'password' => 'password123',
                'password_confirmation' => 'password999',
            ]));

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSeeText('パスワードと一致しません');
    }
    /** @test */
    public function register_success_redirects_to_profile_page_and_creates_user()
    {
        $email = 'ok@example.com';

        $response = $this->post('/register', $this->validPayload([
            'email' => $email,
        ]));

        // ユーザーが作られたこと
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'テスト太郎',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('/verify');
    }
}
