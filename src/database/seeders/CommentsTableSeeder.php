<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 実在する user_id / product_id を取得
        $userIds = User::pluck('id');
        $productIds = Product::pluck('id');

        // ユーザー or 商品が無い場合は何もしない（安全策）
        if ($userIds->isEmpty() || $productIds->isEmpty()) {
            return;
        }

        $records = [];

        // 10件のコメントを作成
        for ($i = 0; $i < 10; $i++) {
            // 時間に差をつけておくとテスト時にわかりやすい
            $createdAt = now()->subMinutes(10 - $i); // 古いコメント → 新しいコメントになるように

            $records[] = [
                'user_id' => $userIds->random(),    // 実在ユーザーからランダム
                'product_id' => $productIds->random(), // 実在商品からランダム
                'comment' => 'テストコメント ' . ($i + 1),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        DB::table('comments')->insert($records);
    }
}
