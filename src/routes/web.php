<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemDetailController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SellController;

// 商品一覧（ホーム）画面表示
Route::get('/', [ItemController::class, 'index'])->name('item.index');
// 商品検索
Route::get('/item/search', [ItemController::class, 'search']);
// 商品詳細画面表示
Route::get('/item/{item_id}', [ItemDetailController::class, 'show']);



// 認証必須
Route::middleware('auth')->group(function(){
    Route::post('/item/{item}/like', [ItemDetailController::class, 'like']);

    Route::post('/item/{item}/comments', [ItemDetailController::class, 'store']);

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show']);

    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store']);

    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'shippingshow']);

    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'shippingupdate']);

    Route::get('/mypage', [MypageController::class, 'mypage']);

    Route::get('/sell', [SellController::class, 'create']);

    Route::post('/sell', [SellController::class, 'store']);

    Route::patch('mypage/profile/update', [ProfileController::class, 'update']);

    Route::get('mypage/profile', [ProfileController::class, 'profile'])->name('mypage.profile');

    Route::post('sell/session', [SellController::class, 'imagePostSession']);
});





















