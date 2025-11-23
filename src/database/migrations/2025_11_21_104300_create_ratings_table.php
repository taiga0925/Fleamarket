<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            // どの取引（商品）に対する評価か
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            // 評価された人（出品者 = usersテーブルのid）
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // 評価した人（購入者 = usersテーブルのid）
            $table->foreignId('rater_id')->constrained('users')->cascadeOnDelete();
            // 星の数 (1~5)
            $table->integer('rating');
            // 評価コメント
            $table->text('comment');
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
        Schema::dropIfExists('ratings');
    }
}
