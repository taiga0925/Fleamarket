<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        // ユーザーAとユーザーBを取得
        $user1 = User::where('email', 'test1@example.com')->first();
        $user2 = User::where('email', 'test2@example.com')->first();

        // ユーザーが見つからない場合のエラーハンドリング（念の為）
        if (!$user1 || !$user2) {
            $this->command->error('Users not found! Please run UsersTableSeeder first.');
            return;
        }

        $items = [
            // --- ユーザーAの出品 (5個) ---
            [
                'user_id' => $user1->id,
                'image' => 'Armani+Mens+Clock.jpg',
                'status' => '良好',
                'item' => '腕時計',
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'money' => 15000,
                'brand' => 'Armani',
                'category_ids' => [1]
            ],
            [
                'user_id' => $user1->id,
                'image' => 'HDD+Hard+Disk.jpg',
                'status' => '目立った傷や汚れなし',
                'item' => 'HDD',
                'detail' => '高速で信頼性の高いハードディスク',
                'money' => 5000,
                'brand' => 'Sony',
                'category_ids' => [2]
            ],
            [
                'user_id' => $user1->id,
                'image' => 'iLoveIMG+d.jpg',
                'status' => 'やや傷や汚れあり',
                'item' => '玉ねぎ3束',
                'detail' => '新鮮な玉ねぎ3束のセット',
                'money' => 300,
                'brand' => '産地直送',
                'category_ids' => [3]
            ],
            [
                'user_id' => $user1->id,
                'image' => 'Leather+Shoes+Product+Photo.jpg',
                'status' => '状態が悪い',
                'item' => '革靴',
                'detail' => 'クラシックなデザインの革靴',
                'money' => 4000,
                'brand' => 'Regal',
                'category_ids' => [1]
            ],
            [
                'user_id' => $user1->id,
                'image' => 'Living+Room+Laptop.jpg',
                'status' => '良好',
                'item' => 'ノートPC',
                'detail' => '高性能なノートパソコン',
                'money' => 45000,
                'brand' => 'Apple',
                'category_ids' => [2]
            ],

            // --- ユーザーBの出品 (5個) ---
            [
                'user_id' => $user2->id,
                'image' => 'Music+Mic+4632231.jpg',
                'status' => '目立った傷や汚れなし',
                'item' => 'マイク',
                'detail' => '高音質のレコーディング用マイク',
                'money' => 8000,
                'brand' => 'Shure',
                'category_ids' => [2]
            ],
            [
                'user_id' => $user2->id,
                'image' => 'Purse+fashion+pocket.jpg',
                'status' => 'やや傷や汚れあり',
                'item' => 'ショルダーバッグ',
                'detail' => 'おしゃれなショルダーバッグ',
                'money' => 3500,
                'brand' => 'Coach',
                'category_ids' => [1]
            ],
            [
                'user_id' => $user2->id,
                'image' => 'Tumbler+souvenir.jpg',
                'status' => '状態が悪い',
                'item' => 'タンブラー',
                'detail' => '使いやすいタンブラー',
                'money' => 500,
                'brand' => 'Starbucks',
                'category_ids' => [3]
            ],
            [
                'user_id' => $user2->id,
                'image' => 'Waitress+with+Coffee+Grinder.jpg',
                'status' => '良好',
                'item' => 'コーヒーミル',
                'detail' => '手動のコーヒーミル',
                'money' => 4000,
                'brand' => 'Kalita',
                'category_ids' => [3]
            ],
            [
                'user_id' => $user2->id,
                'image' => '外出メイクアップセット.jpg',
                'status' => '目立った傷や汚れなし',
                'item' => 'メイクセット',
                'detail' => '便利なメイクアップセット',
                'money' => 2500,
                'brand' => 'MAC',
                'category_ids' => [1]
            ],
        ];

        foreach ($items as $itemData) {
            // category_ids を取り出して配列から削除
            $categoryIds = $itemData['category_ids'] ?? [];
            unset($itemData['category_ids']);

            // 商品を作成
            $item = Item::create($itemData);

            if (!empty($categoryIds)) {
                $item->categories()->attach($categoryIds);
            }
        }
    }
}
