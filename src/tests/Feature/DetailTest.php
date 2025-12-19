<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DetailTest extends TestCase
{
    use RefreshDatabase;

    // ★あなたの詳細ページURLに合わせて変更
    // 例: '/item/' . $productId など
    private function detailPath(int $productId)
    {
        return '/item/' . $productId;
    }

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard(); // fillable/guarded 回避
    }

    /** 必要な情報が表示される（画像/商品名/ブランド/価格/いいね数/コメント数/説明/カテゴリ/状態/コメントユーザー/コメント内容） */
    public function test_detail_page_shows_required_information()
    {
        $seller = User::factory()->create(['name' => '出品者太郎']);

        // 数値が被らないように少し特徴的な値に
        $product = Product::create([
            'user_id' => $seller->id,
            'img_url' => 'https://example.test/images/test-product-999.png',
            'product_name' => 'テスト商品999',
            'brand_name' => 'テストブランドX',
            'price' => 123456,
            'explanation' => 'これは商品説明です',
            'condition' => 4, // 状態が悪い（よくあるマッピング）
        ]);

        // カテゴリ（複数）を付与
        $catAId = $this->createCategory('カテゴリA');
        $catBId = $this->createCategory('カテゴリB');
        $this->attachCategoryToProduct($product->id, $catAId);
        $this->attachCategoryToProduct($product->id, $catBId);

        // いいね（goods）を2件
        $likeUser1 = User::factory()->create();
        $likeUser2 = User::factory()->create();
        $likeUser3 = User::factory()->create();
        $this->likeProduct($product->id, $likeUser1->id);
        $this->likeProduct($product->id, $likeUser2->id);
        $this->likeProduct($product->id, $likeUser3->id);

        // コメントを2件（ユーザー情報＆内容）
        $commentUser1 = User::factory()->create(['name' => 'コメント花子']);
        $commentUser2 = User::factory()->create(['name' => 'コメント次郎']);
        $this->createComment($product->id, $commentUser1->id, '最高です！');
        $this->createComment($product->id, $commentUser2->id, '検討します');

        $res = $this->get($this->detailPath($product->id));
        $res->assertStatus(200);

        // --- 商品基本情報 ---
        $res->assertSee($product->img_url, false); // img srcに含まれる想定
        $res->assertSeeText('テスト商品999');
        $res->assertSeeText('テストブランドX');
        $res->assertSeeText('123,456'); // 表示が「123,456」ならこの行は調整

        $res->assertSeeText('これは商品説明です');

        // --- 商品情報（カテゴリ、状態） ---
        $res->assertSeeText('カテゴリA');
        $res->assertSeeText('カテゴリB');

        // 状態の表示がラベル化されている場合（あなたの実装に合わせて）
        // 例：1=良好,2=目立った傷...,3=やや...,4=状態が悪い
        $res->assertSeeText('状態が悪い');

        // --- いいね数・コメント数 ---
        // 表示に「いいね」「コメント」というラベルがある前提で、順序で確認（被りにくい）
        $res->assertSee('<div class="good">', false);
        $res->assertSee('<p>2</p>', false);

        $res->assertSee('<div class="hukidasi">', false);
        $res->assertSee('<p>3</p>', false);

        // --- コメントユーザー情報・コメント内容 ---
        $res->assertSeeText('コメント花子');
        $res->assertSeeText('最高です！');
        $res->assertSeeText('コメント次郎');
        $res->assertSeeText('検討します');
    }

    /** 複数選択されたカテゴリが表示されているか */
    public function test_detail_page_shows_multiple_categories()
    {
        $seller = User::factory()->create();

        $product = Product::create([
            'user_id' => $seller->id,
            'img_url' => 'https://example.test/images/multi-cat.png',
            'product_name' => 'カテゴリ複数商品',
            'brand_name' => 'ブランド',
            'price' => 999,
            'explanation' => '説明',
            'condition' => 1,
        ]);

        $cat1 = $this->createCategory('カテゴリ1');
        $cat2 = $this->createCategory('カテゴリ2');
        $cat3 = $this->createCategory('カテゴリ3');

        $this->attachCategoryToProduct($product->id, $cat1);
        $this->attachCategoryToProduct($product->id, $cat2);
        $this->attachCategoryToProduct($product->id, $cat3);

        $this->get($this->detailPath($product->id))
            ->assertStatus(200)
            ->assertSeeText('カテゴリ1')
            ->assertSeeText('カテゴリ2')
            ->assertSeeText('カテゴリ3');
    }

    // ------------------------
    // ヘルパー（DB構造に合わせて自動判定）
    // ------------------------

    private function likeProduct(int $productId, int $userId): void
    {
        DB::table('goods')->insert([
            'product_id' => $productId,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createComment(int $productId, int $userId, string $text): void
    {
        // commentsテーブルの本文カラム名を吸収（comment/content/body 等）
        $cols = Schema::getColumnListing('comments');
        $bodyCol = null;
        foreach (['comment', 'content', 'body', 'text', 'message'] as $c) {
            if (in_array($c, $cols, true)) {
                $bodyCol = $c;
                break;
            }
        }
        if ($bodyCol === null) {
            $this->fail('commentsテーブルの本文カラム名が不明です（comment/content/body/text/message のいずれかに合わせてください）');
        }

        $data = [
            'product_id' => $productId,
            'user_id' => $userId,
            $bodyCol => $text,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 余計なカラムが混ざらないようにフィルタ
        $data = $this->onlyExistingColumns('comments', $data);

        DB::table('comments')->insert($data);
    }

    private function createCategory(string $name): int
    {
        if (!Schema::hasTable('categories')) {
            $this->fail('categoriesテーブルが見つかりません。テーブル名が違う場合は createCategory() を調整してください。');
        }

        $cols = Schema::getColumnListing('categories');
        $nameCol = in_array('content', $cols, true) ? 'content' : (in_array('content', $cols, true) ? 'name' : null);

        if ($nameCol === null) {
            $this->fail('categoriesテーブルの名称カラムが不明です（name か category_name に合わせてください）');
        }

        DB::table('categories')->insert([
            $nameCol => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return (int) DB::getPdo()->lastInsertId();
    }

    private function attachCategoryToProduct(int $productId, int $categoryId): void
    {
        $pivotCandidates = ['category_product', 'category_product', 'product_category', 'category_product'];

        $pivot = null;
        foreach ($pivotCandidates as $t) {
            if (Schema::hasTable($t)) {
                $pivot = $t;
                break;
            }
        }

        // 「カテゴリが複数選択」実装が別名の中間テーブルならここを直してください
        if ($pivot === null) {
            // 例：product_categories 等の可能性もあるので追加探索
            foreach (['product_categories', 'category_products', 'categories_products'] as $t) {
                if (Schema::hasTable($t)) {
                    $pivot = $t;
                    break;
                }
            }
        }

        if ($pivot === null) {
            $this->fail('カテゴリ中間テーブルが見つかりません（例: category_product / product_category）。テーブル名に合わせて attachCategoryToProduct() を調整してください。');
        }

        $data = [
            'product_id' => $productId,
            'category_id' => $categoryId,
        ];

        $data = $this->onlyExistingColumns($pivot, $data);

        DB::table($pivot)->insert($data);
    }

    private function onlyExistingColumns(string $table, array $data): array
    {
        $cols = Schema::getColumnListing($table);
        foreach (array_keys($data) as $k) {
            if (!in_array($k, $cols, true))
                unset($data[$k]);
        }
        return $data;
    }
}
