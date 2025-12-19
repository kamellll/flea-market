<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\Model;
use Laravel\Dusk\Browser;

class PayMethodTest extends DuskTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard();
    }

    /** 支払い方法選択：プルダウン選択で表示が切り替わる */
    public function test_can_select_payment_method_and_label_changes()
    {
        // seller作成（products.user_id 必須のため）
        $seller = User::factory()->create();

        $product = Product::create([
            'user_id' => $seller->id,
            'img_url' => 'dummy.jpg',
            'product_name' => '支払い方法テスト商品',
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'explanation' => 'テスト説明',
            'condition' => 1,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/pay') // ←実際のページURLに変更
                // 「カード支払い」を選ぶ
                ->select('@pay-select', '2')
                ->assertSelected('@pay-select', '2')
                ->waitForTextIn('@pay-label', 'カード支払い')
                ->assertSeeIn('@pay-label', 'カード支払い')

                // 「コンビニ払い」に戻す
                ->select('@pay-select', '1')
                ->assertSelected('@pay-select', '1')
                ->waitForTextIn('@pay-label', 'コンビニ払い')
                ->assertSeeIn('@pay-label', 'コンビニ払い');
        });
    }
}
