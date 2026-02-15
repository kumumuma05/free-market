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
        $authUserId = $user->id;

        $items = collect();
        $tradingCount = 0;

        // 取引中の商品表示
        // 未読メッセージのある商品数をカウント表示
        $tradingPurchases = $this->getTradingPurchases($authUserId);

        foreach ($tradingPurchases as $purchase) {
            $unreadCount = $this->countUnreadMessages($purchase, $authUserId);

            if ($unreadCount > 0) {
                $tradingCount++;
            }

            // 未読メッセージ数を表示するのはtradingタブだけ
            if ($tab === 'trading') {
                $purchase->setAttribute('unread_count', $unreadCount);
            }
        }

        // タブ表示
        if ($tab === 'buy') {
            $items = $user->purchasedItems()->latest()->get();
            $tradingPurchases = collect();
        } elseif ($tab === 'sell') {
            $items = $user->items()->latest()->get();
            $tradingPurchases = collect();
        }

        // 評価の表示
        $ratingsCount = $user->receivedRatings()->count();
        $averageRating = null;

        if ($ratingsCount > 0) {
            $averageRating = round($user->receivedRatings()->avg('score'));
        }

        return view('mypage.mypage', compact('items', 'user', 'tab', 'tradingPurchases', 'tradingCount', 'averageRating', 'ratingsCount'));
    }

    /**
     * 取引中の商品を抽出(メッセージが新しい順)
     */
    private function getTradingPurchases(int $authUserId)
    {
        return Purchase::query()
            ->where(function ($statusQuery) use ($authUserId) {
                // 取引中は常に対象
                $statusQuery->where('status', Purchase::STATUS_TRADING)
                    // 評価待ちは「自分が未評価」のときだけ対象
                    ->orWhere(function ($waitingRatingQuery) use ($authUserId) {
                        $waitingRatingQuery
                            ->where('status', Purchase::STATUS_WAITING_RATING)
                            ->whereDoesntHave('ratings', function ($ratingQuery) use ($authUserId) {
                                $ratingQuery->where('rater_id', $authUserId);
                            });
                    });
            })
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
            ->orderByRaw('messages_max_created_at IS NULL ASC') // メッセージあり優先
            ->orderByDesc('messages_max_created_at')            // メッセージ最新順
            ->orderByDesc('purchases.created_at')               // メッセージなしは取引開始順
            ->get();
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