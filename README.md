#環境構築
Dockerビルド
  1. git clone git@github.com:coachtech-material/laravel-docker-template.git  
  2.  mv laravel-docker-template marekt
  3.  docker-compose up -d --build

Laravel環境構築
  1.  docker-compose exec php bash
  2.  composer install
  3.  「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
  4.  cp .env.example .env
  5.  envに以下の環境変数を追加
      DB_CONNECTION=mysql
      DB_HOST=mysql
      DB_PORT=3306
      DB_DATABASE=laravel_db
      DB_USERNAME=laravel_user
      DB_PASSWORD=laravel_pass

アプリケーションキーの作成
  php artisan key:generate

マイグレーションの実行
  php artisan migrate

シーディングの実行
  php artisan db:seed

シンボリックの実行
  php artisan storage:link

メール認証のためのコントローラ作成
  php artisan make:controller Auth/EmailVerificationNotificationController

#使用技術(実行環境)
  PHP 8.4.4
  Laravel Framework 8.83.8
  mysql Ver 15.1 Distrib 10.3.39-MariaDB

＃ER図

<img width="1716" height="889" alt="image" src="https://github.com/user-attachments/assets/94e7557b-07ac-451e-9eae-7545ab84dc7a" />

＃URL
・開発環境：http://localhost/
・phpMyAdmin:：http://localhost:8080/
