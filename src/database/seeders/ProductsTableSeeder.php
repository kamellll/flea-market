<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'id' => 1,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'product_name' => '腕時計',
            'brand_name' => 'Rolax',
            'price' => 15000,
            'explanation' => 'スタイリッシュなデザインのメンズ腕時計',
            'condition' => 1,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 2,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            'product_name' => 'HDD',
            'brand_name' => '西芝',
            'price' => 5000,
            'explanation' => '高速で信頼性の高いハードディスク',
            'condition' => 2,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 3,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            'product_name' => '玉ねぎ3束',
            'brand_name' => 'なし',
            'price' => 300,
            'explanation' => '新鮮な玉ねぎ3束のセット',
            'condition' => 3,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 4,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            'product_name' => '革靴',
            'brand_name' => '',
            'price' => 4000,
            'explanation' => 'クラシックなデザインの革靴',
            'condition' => 4,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 5,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            'product_name' => 'ノートPC',
            'brand_name' => '',
            'price' => 45000,
            'explanation' => '高性能なノートパソコン',
            'condition' => 1,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 6,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            'product_name' => 'マイク',
            'brand_name' => 'なし',
            'price' => 8000,
            'explanation' => '高音質のレコーディング用マイク',
            'condition' => 2,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 7,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'product_name' => 'ショルダーバッグ',
            'brand_name' => '',
            'price' => '3500',
            'explanation' => 'おしゃれなショルダーバッグ',
            'condition' => 3,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 8,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            'product_name' => 'タンブラー',
            'brand_name' => 'なし',
            'price' => 500,
            'explanation' => '使いやすいタンブラー',
            'condition' => 4,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 9,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'product_name' => 'コーヒーミル',
            'brand_name' => 'Starbacks',
            'price' => 4000,
            'explanation' => '手動のコーヒーミル',
            'condition' => 1,
        ];
        DB::table('products')->insert($param);
        $param = [
            'id' => 10,
            'user_id' => 1,
            'img_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'product_name' => 'メイクセット',
            'brand_name' => 'Rolax',
            'price' => 2500,
            'explanation' => '便利なメイクアップセット',
            'condition' => 2,
        ];
        DB::table('products')->insert($param);
    }
}
