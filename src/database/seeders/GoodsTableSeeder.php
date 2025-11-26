<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Good;
use Illuminate\Support\Facades\DB;
class GoodsTableSeeder extends Seeder
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

        // ユーザーか商品が1件も無いなら何もしない（安全策）
        if ($userIds->isEmpty() || $productIds->isEmpty()) {
            return;
        }

        $records = [];

        // 3件の「いいね」レコードを作成
        for ($i = 0; $i < 3; $i++) {
            Good::create([
                'user_id' => $userIds->random(),
                'product_id' => $productIds->random(),
            ]);
        }

        //DB::table('goods')->insert($records);

    }
}
