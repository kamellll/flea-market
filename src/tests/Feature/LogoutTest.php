<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        // ログイン状態にする
        $this->actingAs($user);

        // CSRF対策（テストで419になる環境でも通るようにする）
        $token = 'test-csrf-token';

        $response = $this->withSession(['_token' => $token])
            ->post('/logout', ['_token' => $token]);

        // 未ログインになっていること
        $this->assertGuest();

        // ログアウト後の遷移先
        $response->assertStatus(302);
    }

}
