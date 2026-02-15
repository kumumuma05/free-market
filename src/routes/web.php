<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemDetailController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RatingController;

/**
 * 公開ルート
 */

// 商品一覧（ホーム）画面表示
Route::get('/', [ItemController::class, 'index']);
// 商品検索
Route::get('/item/search', [ItemController::class, 'search']);
// 商品詳細画面表示
Route::get('/item/{item_id}', [ItemDetailController::class, 'show']);

// 決済リダイレクト
Route::get('/purchase/{item}/success', [PurchaseController::class, 'success']);
Route::get('/purchase/{item}/cancel', [PurchaseController::class, 'cancel']);

/**
 * メール認証関係（ログイン必須）
 */
Route::middleware('auth')->group(function () {

    // メール認証誘導画面表示
    Route::get('/email/verify', [EmailVerificationController::class, 'notice']);
    // 認証メール再送
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1');
    // メール認証実行
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

/**
 * アプリ本体（ログイン+メール承認済み+プロフィール完了）
 */
Route::middleware(['auth', 'verified', 'profile.completed'])->group(function () {

    // マイページ表示
    Route::get('/mypage', [MypageController::class, 'show']);

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

    // プロフィール編集画面表示
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    // プロフィール画像のセッションアップロード
    Route::post('/mypage/profile/session', [ProfileController::class, 'imagePostSession']);
    // プロフィール更新
    Route::patch('/mypage/profile', [ProfileController::class, 'update']);

    // 商品出品画面表示
    Route::get('/sell', [SellController::class, 'create']);
    // 商品画像のセッションアップロード
    Route::post('/sell/session', [SellController::class, 'imagePostSession']);
    // 商品出品登録
    Route::post('/sell', [SellController::class, 'store']);

    // 取引チャット画面表示
    Route::get('/transaction/{purchase}', [TransactionController::class,'show']);
    Route::match(['post', 'put'], '/transaction/{purchase}/switch', [TransactionController::class, 'switch']);
    // 取引チャットメッセージ操作
    Route::post('/transaction/{purchase}/messages', [MessageController::class, 'store']);
    Route::get('/transaction/{purchase}/messages/{message}/edit', [MessageController::class, 'editInline']);
    Route::put('/transaction/{purchase}/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/transaction/{purchase}/messages/{message}', [MessageController::class, 'destroy']);

    // 取引完了（メール送信）
    Route::post('/transaction/{purchase}/complete', [TransactionController::class, 'complete']);

    // 評価
    Route::post('/transaction/{purchase}/ratings', [RatingController::class, 'store']);
});