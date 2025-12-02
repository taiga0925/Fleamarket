<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ▼▼▼ 画像コピー処理の追加 ▼▼▼
        $sourcePath = database_path('seeders/images');
        $storagePath = storage_path('app/public/images');

        // コピー先フォルダがなければ作成
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // 画像ファイルをコピー
        if (File::exists($sourcePath)) {
            $files = File::files($sourcePath);
            foreach ($files as $file) {
                File::copy($file->getPathname(), $storagePath . '/' . $file->getFilename());
            }
            $this->command->info('Dummy images copied successfully.');
        } else {
            $this->command->warn('Source image directory not found: ' . $sourcePath);
        }
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // 各シーダーの呼び出し
        $this->call(CotegoriesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
    }
}
