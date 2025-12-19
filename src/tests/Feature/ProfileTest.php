<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    use RefreshDatabase;

    private function createProduct(array $override = []): array
    {
        $now = now();

        $data = array_merge([
            'user_id' => $override['user_id'] ?? null,
            'img_url' => $override['img_url'] ?? 'dummy.png',
            'product_name' => $override['product_name'] ?? 'テスト商品',
            'brand_name' => $override['brand_name'] ?? 'テストブランド',
            'price' => $override['price'] ?? 1000,
            'explanation' => $override['explanation'] ?? '説明文',
            'condition' => $override['condition'] ?? 1,
            'created_at' => $now,
            'updated_at' => $now,
        ], $override);

        $id = DB::table('products')->insertGetId($data);

        return ['id' => $id] + $data;
    }

    private function createProfile(int $userId, array $override = []): array
    {
        $now = now();

        $data = array_merge([
            'user_id' => $userId,
            'postal_code' => '123-4567',
            'address' => '東京都テスト区1-2-3',
            'building' => 'テストマンション101',
            'avatar' => 'avatars/test.png', // nullable
            'created_at' => $now,
            'updated_at' => $now,
        ], $override);

        DB::table('profiles')->insert($data);

        return $data;
    }

    private function createPurchase(int $buyerUserId, int $productId): void
    {
        DB::table('purchases')->insert([
            'user_id' => $buyerUserId,
            'product_id' => $productId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * ① /mypage?page=sell
     * プロフィール画像、ユーザー名、出品した商品が表示される
     */
    public function test_mypage_sell_shows_avatar_username_and_my_listings(): void
    {
        $me = User::factory()->create(['name' => 'テスト太郎']);
        $other = User::factory()->create(['name' => '他人']);

        $profile = $this->createProfile($me->id, [
            'avatar' => 'avatars/sell_user.png',
        ]);

        $myProduct = $this->createProduct([
            'user_id' => $me->id,
            'product_name' => '自分の出品商品A',
        ]);

        $this->createProduct([
            'user_id' => $other->id,
            'product_name' => '他人の出品商品B',
        ]);

        $res = $this->actingAs($me)->get('/mypage?page=sell');

        $res->assertStatus(200);

        // プロフィール画像（HTMLにパスが出る想定）
        $res->assertSee($profile['avatar']);

        // ユーザー名
        $res->assertSee('テスト太郎');

        // 自分の出品商品
        $res->assertSee($myProduct['product_name']);

        // 他人の商品は出ない想定（仕様が違うなら消してOK）
        $res->assertDontSee('他人の出品商品B');
    }

    /**
     * ② /mypage?page=buy
     * プロフィール画像、ユーザー名、購入した商品が表示される（purchases テーブル）
     */
    public function test_mypage_buy_shows_avatar_username_and_my_purchases(): void
    {
        $buyer = User::factory()->create(['name' => '購入者']);
        $seller = User::factory()->create(['name' => '出品者']);

        $profile = $this->createProfile($buyer->id, [
            'avatar' => 'avatars/buy_user.png',
        ]);

        $purchased = $this->createProduct([
            'user_id' => $seller->id,
            'product_name' => '購入した商品',
        ]);

        $notPurchased = $this->createProduct([
            'user_id' => $seller->id,
            'product_name' => '購入してない商品',
        ]);

        // purchases に購入履歴を作る
        $this->createPurchase($buyer->id, $purchased['id']);

        $res = $this->actingAs($buyer)->get('/mypage?page=buy');

        $res->assertStatus(200);

        // プロフィール画像
        $res->assertSee($profile['avatar']);

        // ユーザー名
        $res->assertSee('購入者');

        // 購入した商品
        $res->assertSee($purchased['product_name']);

        // 未購入の商品は出ない想定（仕様が違うなら消してOK）
        $res->assertDontSee($notPurchased['product_name']);
    }
}
