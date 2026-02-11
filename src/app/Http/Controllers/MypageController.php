<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
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

        $items = collect();
        $tradingPurchases = collect();
        $tradingCount = 0;

        if ($tab === 'trading') {
            $authUserId = $user->id;

            $tradingPurchases = Purchase::query()
                ->where('status', Purchase::STATUS_TRADING)
                ->where(function ($query) use ($authUserId) {
                    // 自分が購入者の取引
                    $query->where('buyer_id', $authUserId);

                    // もしくは自分が出品者の取引
                    $query->orWhereHas('item', function ($itemQuery) use ($authUserId) {
                            $itemQuery->where('user_id', $authUserId);
                        });
                })
                ->with('item')
                ->withMax('messages', 'created_at')
                ->orderByDesc('messages_max_created_at')
                ->get();

        } elseif ($tab === 'buy') {
            $items = $user->purchasedItems()->latest()->get();
        } else {
            $items = $user->items()->latest()->get();
        }

        return view('mypage.mypage', compact('items', 'user', 'tab', 'tradingPurchases', 'tradingCount'));
    }
}