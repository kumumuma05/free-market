<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class MypageController extends Controller
{
    /**
     * マイページ表示
     */
    public function mypage(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 'sell');
        if ($page === 'buy') {
            $items = $user->purchasedItems()->latest()->get();
        } else {
            $items = $user->items()->latest()->get();
        }
        return view('mypage.mypage', compact('items', 'user', 'page'));
    }
}