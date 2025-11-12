<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MypageController extends Controller
{
    /**
     * マイページ表示
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $tab = $request->query('page', 'sell');

        if ($tab === 'buy') {
            $items = $user->purchasedItems()->latest()->get();
        } else {
            $items = $user->items()->latest()->get();
        }

        return view('mypage.mypage', compact('items', 'user', 'tab'));
    }
}