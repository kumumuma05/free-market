<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function storeRating(Request $request, Purchase $purchase)
    {
        // アクセス制限
        $this->authorizePurchase($purchase);

        // 取引完了後だけ評価可能
        if (! $purchase->isCompleted()) {
            abort(403);
        }

        // ２重送信防止
        $exists = Rating::where('purchase_id', $purchase->id)
            ->where('rater_id', Auth::id())
            ->exists();

        if ($exists) {
            abort(403);
        }

        $validated = $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        $sellerId = $purchase->item->user_id;
        $buyerId  = $purchase->buyer_id;

        $raterId = Auth::id();
        $rateeId = ($raterId === $buyerId) ? $sellerId : $buyerId;

        // 同一取引で同一評価者は1回まで
        Rating::create([
                'purchase_id' => $purchase->id,
                'rater_id' => $raterId,
                'ratee_id' => $rateeId,
                'score' => $validated['score'],
        ]);

        return redirect('/');
    }

    /**
     * 「購入者」と「出品者」のみが操作できるように制限
     */
    private function authorizePurchase(Purchase $purchase): void
    {
        $userId = Auth::id();
        $sellerId = $purchase->item->user_id;

        if ($purchase->buyer_id !== $userId && $sellerId !== $userId) {
            abort(403);
        }
    }
}
