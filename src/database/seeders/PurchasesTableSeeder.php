<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 実在する user_id と product_id を取得
        $userIds = User::pluck('id');     // 例: [1, 2, 3, ...]
        $productIds = Product::pluck('id');  // 例: [1, 5, 8, ...]

        // ユーザーや商品が存在しない場合は何もしない（エラー防止）
        if ($userIds->isEmpty() || $productIds->isEmpty()) {
            return;
        }

        $records = [];

        // 2件分の購入データを作成
        for ($i = 0; $i < 2; $i++) {
            $records[] = [
                'user_id' => $userIds->random(),    // 実在する user_id からランダム
                'product_id' => $productIds->random(), // 実在する product_id からランダム
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('purchases')->insert($records);
    }
}
