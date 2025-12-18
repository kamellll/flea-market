<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    private string $indexUrl = '/'; // ←商品一覧URLに合わせて変更

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard(); // fillable/guarded 回避（テストではよく使う）
    }

    /** 全商品を取得できる */
    public function test_can_fetch_all_products()
    {
        $sellerA = User::factory()->create();
        $sellerB = User::factory()->create();

        $p1 = $this->createProduct($sellerA->id, '商品A');
        $p2 = $this->createProduct($sellerB->id, '商品B');
        $p3 = $this->createProduct($sellerB->id, '商品C');

        $this->get($this->indexUrl)
            ->assertStatus(200)
            ->assertSeeText($p1->product_name)
            ->assertSeeText($p2->product_name)
            ->assertSeeText($p3->product_name);
    }

    /** 購入済み商品は「Sold」と表示される */
    public function test_purchased_product_displays_sold_label()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $product = $this->createProduct($seller->id, '購入済み商品');

        // purchases にレコードがある = 購入済み
        Purchase::create([
            'product_id' => $product->id,
            'user_id' => $buyer->id,
        ]);

        $this->get($this->indexUrl)
            ->assertStatus(200)
            ->assertSee('購入済み商品')
            ->assertSee('soldout',false);
    }

    /** 自分が出品した商品は表示されない */
    public function test_my_own_products_are_not_shown()
    {
        $me = User::factory()->create();
        $otherSeller = User::factory()->create();

        $myProduct = $this->createProduct($me->id, '自分の商品');
        $otherProduct = $this->createProduct($otherSeller->id, '他人の商品');

        $this->actingAs($me);

        $this->get($this->indexUrl)
            ->assertStatus(200)
            ->assertDontSeeText($myProduct->product_name)
            ->assertSeeText($otherProduct->product_name);
    }

    private function createProduct(int $userId, string $productName): Product
    {
        return Product::create([
            'user_id' => $userId,
            'img_url' => 'dummy.jpg',
            'product_name' => $productName,
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'explanation' => 'テスト説明',
            'condition' => 1,
        ]);
    }
}
