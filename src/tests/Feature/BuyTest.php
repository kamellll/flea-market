<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class BuyTest extends TestCase
{
    use RefreshDatabase;

    // ====== あなたのルートに合わせてここだけ調整可能 ======
    private string $indexPath = '/'; // 商品一覧URL
    private string $profilePurchaseListPath = '/mypage?tab=buy'; // 購入済み一覧（あなたのURLに合わせて）
    // =======================================================

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard();
    }

    private function purchasePagePath(int $productId): string
    {
        return '/product/checkout' . $productId; // GET 購入画面
    }

    private function purchaseActionPath(int $productId): string
    {
        // 「購入する」ボタンの送信先（あなたの実装が POST /purchase ならここを '/purchase' に変更）
        return '/product/checkout' . $productId;
    }

    /** ①「購入する」ボタン押下で購入が完了する */
    public function test_purchase_completes_and_creates_purchase_record()
    {
        $buyer = $this->loginUser();
        $product = $this->createProductByOtherUser();

        // 2) 商品購入画面を開く（/purchase/{product_id} にアクセスできる）
        $this->get($this->purchasePagePath($product->id))
            ->assertStatus(200)
            ->assertSeeText($product->product_name);

        // 3) 「購入する」ボタン押下（purchases が作られる）
        $token = 'test-csrf';
        $res = $this->withSession(['_token' => $token])
            ->post($this->purchaseActionPath($product->id), [
                '_token' => $token,
                'product_id' => $product->id, // フォームに hidden がある想定（無ければ削除してOK）
            ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('purchases', [
            'product_id' => $product->id,
            'user_id' => $buyer->id,
        ]);
    }

    /** ②購入した商品は商品一覧で「sold」（＝soldoutクラス付与）表示される */
    public function test_purchased_product_is_marked_sold_on_index()
    {
        $buyer = $this->loginUser();
        $product = $this->createProductByOtherUser();

        // 購入
        $token = 'test-csrf';
        $this->withSession(['_token' => $token])
            ->post($this->purchaseActionPath($product->id), [
                '_token' => $token,
                'product_id' => $product->id,
            ])->assertStatus(302);

        // 一覧へ
        $res = $this->get($this->indexPath);
        $res->assertStatus(200)
            ->assertSeeText($product->product_name);

        // ※SOLD文字がCSS ::after の場合、テストでは拾えないので「soldout」クラス付与で判定
        $res->assertSee('soldout', false);
    }

    /** ③「プロフィール/購入した商品一覧」に追加されている */
    public function test_purchased_product_appears_in_profile_purchase_list()
    {
        $buyer = $this->loginUser();
        $product = $this->createProductByOtherUser();

        // 購入
        $token = 'test-csrf';
        $this->withSession(['_token' => $token])
            ->post($this->purchaseActionPath($product->id), [
                '_token' => $token,
                'product_id' => $product->id,
            ])->assertStatus(302);

        // 購入した商品一覧へ
        $this->get($this->profilePurchaseListPath)
            ->assertStatus(200)
            ->assertSeeText($product->product_name);
    }

    // --------------------
    // helpers
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

    private function createProductByOtherUser(): Product
    {
        $seller = User::factory()->create();

        return Product::create([
            'user_id' => $seller->id,
            'img_url' => 'dummy.jpg',
            'product_name' => '購入テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'explanation' => 'テスト説明',
            'condition' => 1,
        ]);
    }
}
