<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\VerifyCsrfToken;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /** category を作成（Factory不要） */
    private function createCategory(string $name = 'カテゴリ'): int
    {
        // categories テーブルのカラム名が違う場合はここを調整してください
        // 例：category_name など
        $now = now();
        return DB::table('categories')->insertGetId([
            'content' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function test_user_can_create_product_and_categories_are_saved_in_pivot(): void
    {
        // 1) ユーザーにログイン
        $user = User::factory()->create();

        // 2) 商品出品画面を開く(/sell)
        $this->actingAs($user)
            ->get('/sell')
            ->assertStatus(200);

        // カテゴリを2つ作成（複数選択）
        $cat1 = $this->createCategory('カテゴリA');
        $cat2 = $this->createCategory('カテゴリB');

        // 画像保存：public disk を使う想定（実装が違うなら disk 名を変更）
        Storage::fake('public');

        // GD不要のダミー画像ファイル（拡張子/Content-Typeだけ合わせる）
        $image = UploadedFile::fake()->create('product.png', 10, 'image/png');

        // 3) 各項目に適切な情報を入力して保存する
        // ※ フォームの name 属性に合わせてキー名を調整してください
        $payload = [
            'img_url' => $image,              // 実装が file('img_url') を受ける想定
            'product_name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 12345,
            'explanation' => 'これはテスト用の説明です。',
            'condition' => 2,

            // 複数カテゴリ（name="category_id[]" の想定）
            'categories' => [$cat1, $cat2],
        ];

        // ★保存先が POST /sell の想定
        // もし違うなら route:list で確認してURIを変更してください
        $res = $this->actingAs($user)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/exhibiting', $payload);

        // 成功時はリダイレクトが多い
        $res->assertStatus(302);
        $res->assertSessionHasNoErrors();

        // products に保存されていること（画像は保存パスになる想定）
        $this->assertDatabaseHas('products', [
            'user_id' => $user->id,
            'product_name' => 'テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 12345,
            'explanation' => 'これはテスト用の説明です。',
            'condition' => 2,
        ]);

        // 追加された product_id を取る（最新でOK）
        $productId = DB::table('products')->orderByDesc('id')->value('id');

        $this->assertNotEmpty($productId);

        // category_product に「商品×カテゴリ」分のレコードができること
        $this->assertDatabaseHas('category_product', [
            'product_id' => $productId,
            'category_id' => $cat1,
        ]);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $productId,
            'category_id' => $cat2,
        ]);

        // ついでに「2件ちょうど」も確認（任意）
        $this->assertSame(
            2,
            DB::table('category_product')->where('product_id', $productId)->count(),
            'category_product のレコード数が想定と違います'
        );

        // 画像が保存されていること（DBのimg_urlを使って確認）
        $imgPath = DB::table('products')->where('id', $productId)->value('img_url');
        $this->assertNotEmpty($imgPath);

        $this->assertTrue(
            Storage::disk('public')->exists($imgPath),
            "画像ファイルが保存されていません: {$imgPath}"
        );
    }
}
