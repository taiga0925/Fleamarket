<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // ユーザーA (出品者1)
        $user1 = User::create([
            'name' => 'テスト出品者A',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        Profile::create([
            'user_id' => $user1->id,
            'postcode' => '100-0001',
            'address' => '東京都千代田区',
            'building' => '千代田ビル101'
        ]);

        // ユーザーB (出品者2)
        $user2 = User::create([
            'name' => 'テスト出品者B',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        Profile::create([
            'user_id' => $user2->id,
            'postcode' => '530-0001',
            'address' => '大阪府大阪市北区',
            'building' => '梅田マンション202'
        ]);

        // ユーザーC (購入者)
        $user3 = User::create([
            'name' => 'テスト購入者',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        Profile::create([
            'user_id' => $user3->id,
            'postcode' => '810-0001',
            'address' => '福岡県福岡市中央区',
            'building' => '天神ハイツ303'
        ]);
    }
}
