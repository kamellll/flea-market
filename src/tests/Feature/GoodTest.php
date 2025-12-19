<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class GoodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard(); // fillable/guarded 回避
    }

    // ★あなたの詳細ページURLに合わせて変更
    private function detailPath(int $productId): string
    {
        return '/item/' . $productId;
    }

    /** ① いいねアイコン押下で、いいねとして登録できる */
    public function test_can_like_product_by_pressing_good_icon()
    {
        $user = $this->loginUser();
        $product = $this->createProduct();

        // 2) 商品詳細ページを開く（最初は未いいねアイコン）
        $this->get($this->detailPath($product->id))
            ->assertStatus(200)
            ->assertSee('/images/good_default.png', false);

        // 3) いいねアイコン押下（/good にPOST）
        $this->post('/good', [
            'product_id' => $product->id,
        ])->assertStatus(302);

        // goods テーブルに登録されたこと
        $this->assertDatabaseHas('goods', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /** ② 追加済みのアイコンは色が変化する（pink画像になる） */
    public function test_liked_icon_changes_to_pink()
    {
        $this->loginUser();
        $product = $this->createProduct();

        // いいね押下
        $this->post('/good', [
            'product_id' => $product->id,
        ])->assertStatus(302);

        // 詳細ページを開くと pink になっている
        $this->get($this->detailPath($product->id))
            ->assertStatus(200)
            ->assertSee('/images/good_pink.png', false);
    }

    /** ③ 再度押下で解除できる（goodsレコードが消える） */
    public function test_can_unlike_product_by_pressing_good_icon_again()
    {
        $user = $this->loginUser();
        $product = $this->createProduct();

        // まずいいね
        $this->post('/good', [
            'product_id' => $product->id,
        ])->assertStatus(302);

        $this->assertDatabaseHas('goods', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // もう一度押下（トグル解除想定）
        $this->post('/good', [
            'product_id' => $product->id,
        ])->assertStatus(302);

        // 解除されたこと
        $this->assertDatabaseMissing('goods', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // 表示も default に戻る
        $this->get($this->detailPath($product->id))
            ->assertStatus(200)
            ->assertSee('/images/good_default.png', false);
    }

    // --------------------
    // ヘルパー
    // --------------------

    private function loginUser()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ])->assertStatus(302);

        $this->assertAuthenticatedAs($user);

        return $user;
    }

    private function createProduct(): Product
    {
        $seller = User::factory()->create();

        return Product::create([
            'user_id' => $seller->id,
            'img_url' => 'dummy.jpg',
            'product_name' => 'いいねテスト商品',
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'explanation' => 'テスト説明',
            'condition' => 1,
        ]);
    }
}
