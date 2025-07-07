<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
                    'image' => 'Armani+Mens+Clock.jpg',
                    'status' => '良好',
                    'item' => '腕時計',
                    'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                    'money' => 15000,
                ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'HDD+Hard+Disk.jpg',
                    'status' => '目立った傷や汚れなし',
                    'item' => 'HDD',
                    'detail' => '高速で信頼性の高いハードディスク',
                    'money' => 5000,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'iLoveIMG+d.jpg',
                    'status' => 'やや傷や汚れあり',
                    'item' => '玉ねぎ3束',
                    'detail' => '新鮮な玉ねぎ3束のセット',
                    'money' => 300,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'Leather+Shoes+Product+Photo.jpg',
                    'status' => '状態が悪い',
                    'item' => '革靴',
                    'detail' => 'クラシックなデザインの革靴',
                    'money' => 4000,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'Living+Room+Laptop.jpg',
                    'status' => '良好',
                    'item' => 'ノートPC',
                    'detail' => '高性能なノートパソコン',
                    'money' => 45000,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'Music+Mic+4632231.jpg',
                    'status' => '目立った傷や汚れなし',
                    'item' => 'マイク',
                    'detail' => '高音質のレコーディング用マイク',
                    'money' => 8000,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'Purse+fashion+pocket.jpg',
                    'status' => 'やや傷や汚れあり',
                    'item' => 'ショルダーバッグ',
                    'detail' => 'おしゃれなショルダーバッグ',
                    'money' => 3500,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'Tumbler+souvenir.jpg',
                    'status' => '状態が悪い',
                    'item' => 'タンブラー',
                    'detail' => '使いやすいタンブラー',
                    'money' => 500,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => 'Waitress+with+Coffee+Grinder.jpg',
                    'status' => '良好',
                    'item' => 'コーヒーミル',
                    'detail' => '手動のコーヒーミル',
                    'money' => 4000,
        ];
        DB::table('items')->insert($param);

        $param = [
                    'image' => '外出メイクアップセット.jpg',
                    'status' => '目立った傷や汚れなし',
                    'item' => 'メイクセット',
                    'detail' => '便利なメイクアップセット',
                    'money' => 2500,
        ];
        DB::table('items')->insert($param);
    }
}
