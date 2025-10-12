<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemDetailController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\SellController;



Route::get('/', [ItemController::class, 'index'])->name('item.index');
Route::get('/item/search', [ItemController::class, 'search']);

Route::get('/item/{item_id}', [ItemDetailController::class, 'show']);

Route::get('/purchase/{item_id}', [PurchaseController::class, 'order']);
Route::post('/item/{item}/like', [LikeController::class, 'like']);

Route::post('/item/{item}/comments', [CommentController::class, 'store']);

Route::get('/mypage', [MypageController::class, 'mypage']);

Route::get('mypage/profile', [ProfileController::class, 'profile'])->name('mypage.profile');

Route::patch('mypage/profile/update', [ProfileController::class, 'update']);

Route::get('/sell', [SellController::class, 'create']);

Route::post('/sell', [SellController::class, 'store']);
