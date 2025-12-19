<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private string $indexPath = '/'; // ← 商品一覧のURLに合わせて変更

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard(); // fillable/guarded 回避
    }

    /** 「商品名」で部分一致検索ができる */
    public function test_can_search_by_partial_match_of_product_name()
    {
        $seller = User::factory()->create();

        $match = $this->createProduct($seller->id, 'りんごジュース');
        $noMatch = $this->createProduct($seller->id, 'みかんジュース');

        $this->get($this->indexPath . '?keyword=りんご')
            ->assertStatus(200)
            ->assertSeeText($match->product_name)
            ->assertDontSeeText($noMatch->product_name);
    }

    /** 検索状態がマイリストでも保持されている */
    public function test_search_keyword_is_kept_in_session_and_applies_to_mylist()
    {
        // ログイン（mylist はログイン必須）
        $me = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => $me->email,
            'password' => 'password123',
        ])->assertStatus(302);

        $seller = User::factory()->create();

        // いいねした商品（片方は検索にヒット、片方はヒットしない）
        $likedMatch = $this->createProduct($seller->id, 'りんごパイ');
        $likedNoMatch = $this->createProduct($seller->id, 'チョコパイ');

        // いいねしていないが検索にヒットする商品（mylistでは出ない想定）
        $unlikedMatch = $this->createProduct($seller->id, 'りんごケーキ');

        // goods に1レコード作る = いいね
        DB::table('goods')->insert([
            'user_id' => $me->id,
            'product_id' => $likedMatch->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('goods')->insert([
            'user_id' => $me->id,
            'product_id' => $likedNoMatch->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ① まず all タブで検索して「keyword」をセッションに保持させる
        $this->get($this->indexPath . '?keyword=りんご')
            ->assertStatus(200);

        // ② 次に mylist へ（keyword を付けない）→ セッションの検索状態が効く
        $this->get($this->indexPath . '?tab=mylist')
            ->assertStatus(200)
            ->assertSeeText($likedMatch->product_name)      // いいね + ヒット → 表示
            ->assertDontSeeText($likedNoMatch->product_name) // いいね + 非ヒット → 非表示
            ->assertDontSeeText($unlikedMatch->product_name); // 非いいね + ヒット → 非表示
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
