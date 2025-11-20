<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
class Category_productSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 対象の 10 商品（全件 or 上位10件など）
        $products = Product::take(10)->get();

        // 存在するカテゴリID一覧を取得（例： [1,2,3,4,...] ）
        $categoryIds = Category::pluck('id');

        foreach ($products as $product) {
            // 1商品につき 1〜3 個くらいのカテゴリをランダムに付ける例
            $attachIds = $categoryIds->random(rand(1, 3))->all();

            // すでに紐づけがある場合は syncWithoutDetaching() でもOK
            $product->categories()->syncWithoutDetaching($attachIds);

            // まだ何も入ってないなら attach() でもいい
            // $product->categories()->attach($attachIds);
        }
    }
}
