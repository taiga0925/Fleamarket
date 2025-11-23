<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            // 送信者 (usersテーブルのidを参照)
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            // 受信者 (usersテーブルのidを参照)
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            // どの商品の取引チャットか
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            // メッセージ本文 
            $table->string('message')->nullable();
            // 画像ファイル名
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
