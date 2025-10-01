<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class itemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Item::create([
            'product_name' => '腕時計',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'condition' => 1,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'is_sold' => true,
            'user_id' => 1,
        ]);
        Item::create([
            'product_name' => 'HDD',
            'brand' => '西芝',
            'description' => '高速で信頼性の高いハードディスク',
            'price' => 5000,
            'condition' => 2,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            'is_sold' => true,
            'user_id' => 2,
        ]);
        Item::create([
            'product_name' => '玉ねぎ3束',
            'brand' => 'なし',
            'description' => '新鮮な玉ねぎ3束のセット',
            'price' => 300,
            'condition' => 3,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            'is_sold' => false,
            'user_id' => 3,
        ]);
        Item::create([
            'product_name' => '革靴',
            'brand' => '',
            'description' => 'クラシックなデザインの革靴',
            'price' => 4000,
            'condition' => 4,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            'is_sold' => false,
            'user_id' => 1,
        ]);
        Item::create([
            'product_name' => 'ノートPC',
            'brand' => '',
            'description' => '高性能なノートパソコン',
            'price' => 45000,
            'condition' => 1,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            'is_sold' => false,
            'user_id' => 2,
        ]);
        Item::create([
            'product_name' => 'マイク',
            'brand' => 'なし',
            'description' => '高音質のレコーディング用マイク',
            'price' => 8000,
            'condition' => 2,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            'is_sold' => false,
            'user_id' => 3,
        ]);
        Item::create([
            'product_name' => 'ショルダーバッグ',
            'brand' => '',
            'description' => 'おしゃれなショルダーバッグ',
            'price' => 3500,
            'condition' => 3,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'is_sold' => false,
            'user_id' => 1,
        ]);
        Item::create([
            'product_name' => 'タンブラー',
            'brand' => 'なし',
            'description' => '使いやすいタンブラー',
            'price' => 500,
            'condition' => 4,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            'is_sold' => false,
            'user_id' => 2,
        ]);
        Item::create([
            'product_name' => 'コーヒーミル',
            'brand' => 'Starbacks',
            'description' => '手動のコーヒーミル',
            'price' => 4000,
            'condition' => 1,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'is_sold' => false,
            'user_id' => 3,
        ]);
        Item::create([
            'product_name' => 'メイクセット',
            'brand' => '',
            'description' => '便利なメイクアップセット',
            'price' => 2500,
            'condition' => 2,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/外出メイクアップセット.jpg',
            'is_sold' => false,
            'user_id' => 1,
        ]);
    }
}
