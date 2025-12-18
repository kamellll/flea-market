<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    // 商品一覧のパス（あなたのルーティングに合わせて変更）
    private string $indexPath = '/';

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard(); // fillable/guarded をテストでは回避
    }

    /** いいねした商品だけが表示される */
    public function test_mylist_shows_only_liked_products_when_logged_in()
    {
        // ログインユーザー作成（実際に /login でログインする）
        $me = $this->createAndLoginUser();

        // 出品者（別ユーザー）
        $seller = User::factory()->create();

        // 商品作成（自分の出品は一覧から除外される仕様なので seller の商品を作る）
        $liked = $this->createProduct($seller->id, 'いいねした商品');
        $notLiked = $this->createProduct($seller->id, 'いいねしてない商品');

        // goods に 1レコード作る = いいね
        DB::table('goods')->insert([
            'user_id' => $me->id,
            'product_id' => $liked->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // tab=mylist でアクセス
        $this->get($this->indexPath . '?tab=mylist')
            ->assertStatus(200)
            ->assertSeeText('いいねした商品')
            ->assertDontSeeText('いいねしてない商品');
    }

    /** 購入済み商品は「Sold」（＝soldoutクラス付与）と表示される */
    public function test_mylist_purchased_product_has_soldout_class()
    {
        $me = $this->createAndLoginUser();
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $product = $this->createProduct($seller->id, '購入済み商品');

        // いいね
        DB::table('goods')->insert([
            'user_id' => $me->id,
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // purchases に 1レコード作る = 購入済み
        DB::table('purchases')->insert([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // mylist表示
        $res = $this->get($this->indexPath . '?tab=mylist');
        $res->assertStatus(200)
            ->assertSeeText('購入済み商品');

        // CSS ::after の "SOLD" はHTMLに出ないので、soldoutクラスが付いているかで判定
        // （Blade側で <div class="product-card__image-wrapper soldout"> のように付与されている前提）
        $res->assertSee('soldout', false);
    }

    /** 未ログインの場合は何も表示されない */
    public function test_mylist_shows_nothing_when_guest()
    {
        $seller = User::factory()->create();
        $product = $this->createProduct($seller->id, 'いいねした商品');

        // goods に入れても、未ログインなら mylist は 0件表示の仕様
        DB::table('goods')->insert([
            'user_id' => User::factory()->create()->id,
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get($this->indexPath . '?tab=mylist')
            ->assertStatus(200)
            ->assertDontSeeText('いいねした商品');
    }

    // --------------------
    // ヘルパー
    // --------------------

    private function createAndLoginUser()
    {
        $password = 'password123';

        $user = User::factory()->create([
            'email' => 'me@example.com',
            'password' => Hash::make($password),
        ]);

        // 実際にログイン処理
        $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ])->assertStatus(302);

        $this->assertAuthenticated();

        return $user;
    }

    private function createProduct(int $userId, string $name): Product
    {
        return Product::create([
            'user_id' => $userId,
            'img_url' => 'dummy.jpg',
            'product_name' => $name,
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'explanation' => 'テスト説明',
            'condition' => 1,
        ]);
    }
}
