<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemDetailController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SellController;

// 商品一覧（ホーム）画面表示
Route::get('/', [ItemController::class, 'index']);

// 商品検索
Route::get('/item/search', [ItemController::class, 'search']);

// 商品詳細画面表示
Route::get('/item/{item_id}', [ItemDetailController::class, 'show']);


// 認証必須
Route::middleware('auth')->group(function(){

    // いいね登録
    Route::post('/item/{item}/like', [ItemDetailController::class, 'like']);
    // コメント登録
    Route::post('/item/{item}/comments', [ItemDetailController::class, 'store']);

    // 商品購入画面表示
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show']);
    // 商品購入
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store']);
    // 配送先住所変更画面表示
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'shippingShow']);
    // 配送先変更のセッション登録
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'shippingUpdate']);

    // マイページ表示
    Route::get('/mypage', [MypageController::class, 'show']);
    // プロフィール編集画面表示
    Route::get('mypage/profile', [ProfileController::class, 'edit']);
    // プロフィール画像のセッションアップロード
    Route::post('mypage/profile/session', [ProfileController::class, 'imagePostSession']);
    // プロフィール更新
    Route::patch('mypage/profile/update', [ProfileController::class, 'update']);

    // 商品出品画面表示
    Route::get('/sell', [SellController::class, 'create']);
    // 商品画像のセッションアップロード
    Route::post('sell/session', [SellController::class, 'imagePostSession']);
    // 商品出品登録
    Route::post('/sell', [SellController::class, 'store']);
});





















