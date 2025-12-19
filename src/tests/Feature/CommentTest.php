<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    // ===== あなたの環境に合わせてここだけ調整 =====
    private string $commentStorePath = '/comment';   // コメント送信のPOST先（例: /comment）
    private string $commentField = 'comment';    // textarea の name（例: comment）
    private string $detailBasePath = '/item/'; // 詳細URLのベース（例: /products/{id} or /item/{id}）

    // バリデーションメッセージ（あなたの messages.php / FormRequest に合わせて）
    private string $requiredMsg = 'コメントを入力してください';
    private string $maxMsg = 'コメントの最大文字数は255文字です';
    // ============================================

    private function detailPath(int $productId): string
    {
        return $this->detailBasePath . $productId;
    }

    /** ① ログイン済みのユーザーはコメントを送信できる */
    public function test_authenticated_user_can_post_comment()
    {
        $user = $this->loginUser();
        $product = $this->createProduct();

        $bodyCol = $this->commentBodyColumn();

        $payload = [
            'product_id' => $product->id,
            $this->commentField => 'テストコメントです',
        ];

        $this->post($this->commentStorePath, $payload)
            ->assertStatus(302);

        $this->assertDatabaseHas('comments', array_merge([
            'product_id' => $product->id,
            'user_id' => $user->id,
        ], [
            $bodyCol => 'テストコメントです',
        ]));
    }

    /** ② ログイン前のユーザーはコメントを送信できない */
    public function test_guest_cannot_post_comment()
    {
        $product = $this->createProduct();

        $this->post($this->commentStorePath, [
            'product_id' => $product->id,
            $this->commentField => 'ゲストコメント',
        ])->assertStatus(302); // 多くは /login にリダイレクト（middleware auth）

        // 送信されていないこと
        $this->assertDatabaseCount('comments', 0);
    }

    /** ③ コメントが入力されていない場合、バリデーションメッセージが表示される */
    public function test_comment_required_shows_validation_message()
    {
        $this->loginUser();
        $product = $this->createProduct();

        $this->from($this->detailPath($product->id))
            ->followingRedirects()
            ->post($this->commentStorePath, [
                'product_id' => $product->id,
                $this->commentField => '',
            ])
            ->assertStatus(200)
            ->assertSeeText($this->requiredMsg);
    }

    /** ④ コメントが255字以上の場合、バリデーションメッセージが表示される */
    public function test_comment_over_255_shows_validation_message()
    {
        $this->loginUser();
        $product = $this->createProduct();

        $tooLong = Str::repeat('あ', 256);

        $this->from($this->detailPath($product->id))
            ->followingRedirects()
            ->post($this->commentStorePath, [
                'product_id' => $product->id,
                $this->commentField => $tooLong,
            ])
            ->assertStatus(200)
            ->assertSeeText($this->maxMsg);
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

    private function createProduct()
    {
        $seller = User::factory()->create();

        return Product::create([
            'user_id' => $seller->id,
            'img_url' => 'dummy.jpg',
            'product_name' => 'コメントテスト商品',
            'brand_name' => 'テストブランド',
            'price' => 1000,
            'explanation' => 'テスト説明',
            'condition' => 1,
        ]);
    }

    /**
     * comments テーブルの本文カラム名を自動推定
     * （comment/content/body/text/message のどれかを想定）
     */
    private function commentBodyColumn()
    {
        if (!Schema::hasTable('comments')) {
            $this->fail('commentsテーブルが見つかりません。');
        }

        $cols = Schema::getColumnListing('comments');

        foreach (['comment', 'content', 'body', 'text', 'message'] as $c) {
            if (in_array($c, $cols, true))
                return $c;
        }

        $this->fail('commentsテーブルの本文カラム名が不明です（comment/content/body/text/message のいずれかに合わせてください）');
    }
}
