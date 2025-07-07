<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;

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

Route::middleware('auth')->group(function () {

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

});

Route::get('/', [ItemController::class, 'index']);
Route::get('/search', [ItemController::class, 'search']);
Route::get('/item/{id}', [ItemController::class, 'item']);
