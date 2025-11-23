<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ItemController::class, 'index']);
Route::get('/search', [ItemController::class, 'search']);
Route::get('/item/{id}', [ItemController::class, 'item']);

// 認証メール再送ルート
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, '__invoke'])
    ->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    // マイページ関連
    Route::prefix('mypage')->group(function () {
        Route::get('/', [MypageController::class, 'index']);
        Route::get('/profile', [MypageController::class, 'profile']);
        Route::post('/profile/update', [MypageController::class, 'update']);
    });

    // 購入関連
    Route::prefix('purchase')->group(function () {
        Route::get('/{item_id}', [PurchaseController::class, 'index']);
        Route::get('/address/{item_id}', [PurchaseController::class, 'address']);
        Route::post('/address/update/{item_id}', [PurchaseController::class, 'update']);
        Route::post('/payment/{item_id}', [PurchaseController::class, 'payment']);
        Route::post('/decide/{item_id}', [PurchaseController::class, 'purchase']);
    });

    // 商品関連
    Route::prefix('item')->group(function () {
        Route::post('/like/{item_id}', [ItemController::class, 'like']);
        Route::delete('/unlike/{item_id}', [ItemController::class, 'unlike']);
        Route::get('/comment/{item_id}', [ItemController::class, 'list']);
        Route::post('/comment/{item_id}', [ItemController::class, 'comment']);
    });

    // 出品関連
    Route::get('/sell', [SellController::class, 'index']);
    Route::get('/sell/{item_id}', [SellController::class, 'index']);
    Route::post('/sell', [SellController::class, 'create']);

    // チャット機能
    Route::prefix('item/{item_id}/chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::post('/', [ChatController::class, 'store'])->name('chat.store'); // 送信用

        // メッセージ削除
        Route::delete('/{chat_id}', [ChatController::class, 'destroy'])->name('chat.destroy');
        // メッセージ編集画面
        Route::get('/{chat_id}/edit', [ChatController::class, 'edit'])->name('chat.edit');
        // メッセージ更新処理
        Route::patch('/{chat_id}', [ChatController::class, 'update'])->name('chat.update');
    });

    // 評価機能
    Route::prefix('item/{item_id}/rating')->group(function () {
        Route::post('/', [RatingController::class, 'store'])->name('rating.store');
    });
});
