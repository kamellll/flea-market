<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\VerifyCsrfToken;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function createProfile(int $userId, array $override = []): void
    {
        $now = now();

        DB::table('profiles')->insert(array_merge([
            'user_id' => $userId,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => '旧ビル101',
            'avatar' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $override));
    }

    public function test_profile_avatar_upload_is_saved_and_reflected(): void
    {
        $user = User::factory()->create(['name' => '旧ユーザー名']);
        $this->createProfile($user->id);

        Storage::fake('public');

        // PNG風のダミーファイル（中身はダミーでOK）
        $file = UploadedFile::fake()->create('avatar.png', 10, 'image/png');

        $res = $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post('/mypage/profile/update', [
                'name' => '新ユーザー名',
                'postal_code' => '987-6543',
                'address' => '大阪府テスト市4-5-6',
                'building' => '新ビル202',
                'avatar' => $file,
            ]);

        $res->assertStatus(302);
        $res->assertSessionHasNoErrors();

        // DBに保存パスが入っているか
        $avatarPath = DB::table('profiles')->where('user_id', $user->id)->value('avatar');
        $this->assertNotEmpty($avatarPath);

        // 実ファイル（fake public）に保存されたか
        $this->assertTrue(
            Storage::disk('public')->exists($avatarPath),
            "Avatar file not found in public disk: {$avatarPath}"
        );

        // 画面で表示されているか（HTMLにパス/URLが出る前提。実装によっては Storage::url() で変換されるので要調整）
        $page = $this->actingAs($user)->get('/mypage/profile');
        $page->assertStatus(200);
        $page->assertSee('新ユーザー名');
    }
}
