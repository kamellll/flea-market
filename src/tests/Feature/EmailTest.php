<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ① 会員登録後、認証メール（VerifyEmail通知）が送られる
     *  - POST /register で登録
     *  - /email/verify にリダイレクトされる想定
     */
    public function test_register_sends_verification_email(): void
    {
        Notification::fake();

        $payload = [
            'name' => 'テスト太郎',
            'email' => 'a@a.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $res = $this->post('/register', $payload);

        // 登録後はメール認証誘導画面へ、という要件に合わせる
        $res->assertStatus(302);
        $res->assertRedirect('/email/verify');

        $user = User::where('email', 'a@a.com')->firstOrFail();

        // 未認証のまま
        $this->assertNull($user->email_verified_at);

        // 認証メール（VerifyEmail通知）が送られた
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * ② メール認証誘導画面（/email/verify）に「認証はこちらから」ボタンが表示される
     *
     * ※「ボタンがどこへ遷移するか」は実装依存なので、下の A/B のどちらかを採用してください。
     */
    public function test_verify_notice_page_has_button(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $res = $this->actingAs($user)->get('/email/verify');

        $res->assertStatus(200);
        $res->assertSee('認証はこちらから');

        // -------------------------
        // A) ボタンが Mailhog UI に遷移する実装の場合（例）
        // 例：href="http://localhost:8025"
        // -------------------------
        $res->assertSee('8025');

        // -------------------------
        // B) ボタンが「認証メール再送」(POST /email/verification-notification) の場合
        // この場合は画面に form action があるはず
        // -------------------------
        // $res->assertSee('/email/verification-notification');
    }

    /**
     * ③ 認証リンクにアクセスすると認証完了し、プロフィール設定画面へ遷移する
     *  - 署名付きURLをテスト側で生成してアクセス（メール本文を開かなくても検証可能）
     */
    public function test_email_verification_completes_and_redirects_to_profile_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Laravel標準の認証URL（verification.verify）を生成
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $res = $this->actingAs($user)->get($verifyUrl);

        // ★ 認証後の遷移先はあなたの要件「プロフィール設定画面」に合わせて変更してください
        // 例：/mypage/profile など
        $res->assertStatus(302);
        $res->assertRedirect('/mypage/profile'); // ←必要ならここを変更

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }
    public function test_register_really_sends_mail_to_mailhog(): void
    {
        // Mailhog を空にする（任意）
        Http::delete('http://mailhog:8025/api/v1/messages');

        $this->post('/register', [
            'name' => 'テスト太郎',
            'email' => 'a@a.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(302);

        $json = Http::get('http://mailhog:8025/api/v2/messages')->json();

        // メールが1通以上ある
        $this->assertGreaterThanOrEqual(1, $json['total'] ?? 0);

        // 宛先に a@a.com が含まれる（Mailhogの構造に合わせた雑チェック）
        $body = json_encode($json, JSON_UNESCAPED_UNICODE);
        $this->assertStringContainsString('a@a.com', $body);
    }
}
