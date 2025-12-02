# coachtechフリマ

## アプリケーション概要
出品・購入機能を持ったフリーマーケットアプリケーションです。
商品の出品、購入、取引チャット、評価機能などを備えています。

## 作成した目的
フリマアプリの基本的な機能（出品・購入）に加え、ユーザー間のコミュニケーション（チャット）や信頼性担保（評価）の実装を通して、Webアプリケーション開発の応用力を身につけるため。

## アプリケーションURL
- **開発環境**: http://localhost/
- **メール確認 (Mailtrap)**: https://mailtrap.io/ (開発者のアカウントでログイン)

## 機能一覧
- **会員登録・ログイン・ログアウト** (Laravel Fortify使用)
- **メール認証機能**
  - 会員登録直後に認証メールを送信
  - メール内のリンクをクリックするまで機能制限（マイページ等へのアクセス不可）
- **商品一覧表示**
  - おすすめ（全商品）、マイリスト（いいねした商品）の切り替え
- **商品詳細表示**
- **商品出品機能**
  - 画像アップロード、カテゴリ選択（多対多）、状態設定
- **商品購入機能**
  - 支払い方法選択、配送先住所変更
- **マイページ機能**
  - 出品した商品、購入した商品、取引中の商品一覧
  - プロフィール編集（画像、住所設定）
  - 評価平均の表示（星評価）
- **商品検索機能**
- **商品お気に入り（いいね）機能**
- **コメント機能**
- **取引チャット機能**
  - メッセージ送信、画像送信
  - 取引完了ボタン（評価モーダル）
  - 未読メッセージ数の表示
- **取引評価機能**
  - 5段階評価、コメント送信
- **メール通知機能**
  - 取引完了（評価）時に出品者へメール通知

## 使用技術(実行環境)
- **言語/フレームワーク**: PHP  8.4.4 , Laravel  8.83.8
- **データベース**: MySQL Ver 15.1
- **インフラ**: Docker (Laravel Sail / docker-compose)
- **認証機能**: Laravel Fortify
- **メールサーバー**: Mailtrap (開発環境用SMTPサーバー)

## テーブル設計

### users テーブル
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| name | string | NOT NULL |
| email | string | NOT NULL, UNIQUE |
| password | string | NOT NULL |
| img_url | string | NULLable |
| email_verified_at | timestamp | NULLable (メール認証用) |

### profiles テーブル
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| user_id | foreignId | Foreign Key (users) |
| postcode | string | NOT NULL |
| address | string | NOT NULL |
| building | string | NULLable |

### items テーブル
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| user_id | foreignId | Foreign Key (users) |
| item | string | NOT NULL (商品名) |
| brand | string | NULLable |
| money | integer | NOT NULL |
| detail | text | NOT NULL |
| image | string | NOT NULL |
| status | string | NOT NULL (商品の状態) |

### categories テーブル
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| category | string | NOT NULL |
| parent_id | foreignId | NULLable (自己参照) |

### category_items テーブル (中間テーブル)
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| item_id | foreignId | Foreign Key (items) |
| category_id | foreignId | Foreign Key (categories) |

### sold_items テーブル (中間テーブル: 購入履歴)
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| item_id | foreignId | Foreign Key (items) |
| user_id | foreignId | Foreign Key (users: 購入者) |
| method | string | NOT NULL (支払方法) |

### likes テーブル (中間テーブル)
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| user_id | foreignId | Foreign Key (users) |
| item_id | foreignId | Foreign Key (items) |

### comments テーブル
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| user_id | foreignId | Foreign Key (users) |
| item_id | foreignId | Foreign Key (items) |
| comment | text | NOT NULL |

### chats テーブル (新規追加)
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| sender_id | foreignId | Foreign Key (users) |
| receiver_id | foreignId | Foreign Key (users) |
| item_id | foreignId | Foreign Key (items) |
| message | string | NULLable |
| image | string | NULLable |
| is_read | boolean | DEFAULT false (既読フラグ) |

### ratings テーブル (新規追加)
| Column | Type | Options |
| ------ | ---- | ------- |
| id | bigint | PRIMARY KEY |
| item_id | foreignId | Foreign Key (items) |
| rater_id | foreignId | Foreign Key (users: 評価した人) |
| user_id | foreignId | Foreign Key (users: 評価された人) |
| rating | integer | NOT NULL (1-5) |
| comment | text | NULLable |

## ER図
<img width="800" alt="ER図" src="https://github.com/taiga0925/Fleamarket/blob/main/ER.drawio.png?raw=true" />

## 環境構築

**1. リポジトリをクローン**
```bash
git clone [https://github.com/taiga0925/Fleamarket.git](https://github.com/taiga0925/Fleamarket.git)
...```
```bash
2. プロジェクトディレクトリに移動
cd Fleamarket
...
3. Dockerコンテナのビルドと起動
docker-compose up -d --build

4. PHPコンテナにログイン これ以降のコマンドはコンテナ内で実行します。
docker-compose exec php bash

5. コンテナ内でのセットアップ
# 依存パッケージのインストール
composer install
# 環境変数の設定
cp .env.example .env
# ※ここで必要に応じて .env ファイル内のDB接続情報やMAIL設定を変更してください
# アプリケーションキーの生成
php artisan key:generate
# シンボリックリンクの作成 (画像表示に必須)
php artisan storage:link
# マイグレーションとシーディング (初期データ作成)
php artisan migrate:fresh --seed

テスト用アカウント情報
シーダーによって以下の3つのアカウントが作成されます。
役割                    メールアドレス    パスワード    状態
ユーザーA (出品者)  test1@example.com  password  商品5点出品済み
ユーザーB (出品者)  test2@example.com  password  商品5点出品済み
ユーザーC (購入者)  test3@example.com  password  出品なし、購入専用（初期状態）
