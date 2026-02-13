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

        // タブ表示
        if ($tab === 'trading') {
            $authUserId = $user->id;

            $tradingPurchases = Purchase::query()
                ->whereIn('status', [
                    Purchase::STATUS_TRADING,
                    Purchase::STATUS_WAITING_RATING
                ])
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

                // 各取引の未読メッセージを算出
                // 未読が1件以上ある取引数を集計
                foreach ($tradingPurchases as $purchase) {
                    $purchase->unread_count = $this->countUnreadMessages($purchase, $authUserId);

                    if ($purchase->unread_count > 0) {
                        $tradingCount++;
                    }
                }
        } elseif ($tab === 'buy') {
            $items = $user->purchasedItems()->latest()->get();
        } else {
            $items = $user->items()->latest()->get();
        }

        return view('mypage.mypage', compact('items', 'user', 'tab', 'tradingPurchases', 'tradingCount'));
    }

    /**
     * 取引相手の未読のメッセージを数える
     */
    private function countUnreadMessages(Purchase $purchase, int $authUserId): int
    {
        $isBuyer = ($purchase->buyer_id === $authUserId);

        if ($isBuyer) {
            $lastReadAt = $purchase->buyer_last_read_at;
        } else {
            $lastReadAt = $purchase->seller_last_read_at;
        }

        $query = $purchase->messages()->where('sender_id', '!=', $authUserId);

        if ($lastReadAt !== null) {
            $query->where('created_at', '>', $lastReadAt);
        }

        return $query->count();
    }
}