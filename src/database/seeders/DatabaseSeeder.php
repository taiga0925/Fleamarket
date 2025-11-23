<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CotegoriesTableSeeder::class);

        //  ユーザー作成 (今回新規作成するファイル)
        $this->call(UsersTableSeeder::class);

        $this->call(ItemsTableSeeder::class);
    }
}
