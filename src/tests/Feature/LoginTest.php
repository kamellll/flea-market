<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メールアドレス未入力 → バリデーションメッセージ
     */
    public function test_email_required()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);

        $this->get('/login')
            ->assertStatus(200)
            ->assertSeeText('メールアドレスを入力してください');

        // エラーメッセージ本文まで固定したい場合（日本語化している前提）
        // $this->assertSame('メールアドレスは必須です。', session('errors')->first('email'));
    }
    public function test_password_required()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'ok@example.com',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);

        $this->get('/login')
            ->assertStatus(200)
            ->assertSeeText('パスワードを入力してください');
    }
    public function test_invalid_login()
    {
        User::factory()->create([
            'email' => 'ok@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'ok@example.com',
            'password' => 'aaaaaaaaaa',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);

        $this->get('/login')
            ->assertStatus(200)
            ->assertSeeText('ログイン情報が登録されていません');
    }
    public function test_login_ok()
    {
        $user = User::factory()->create([
            'email' => 'ok@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'ok@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertStatus(302);
    }
}
