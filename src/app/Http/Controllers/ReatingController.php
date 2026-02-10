<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReatingController extends Controller
{
    public function storeRating(Request $request, Purchase $purchase)
    {
        $this->authorizePurchase($purchase);

        // 取引完了後だけ評価可能、など仕様があるならここでチェック
        abort_unless($purchase->isCompleted(), 403);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        // 評価相手（取引相手）を決める
        $raterId = Auth::id();

        $sellerId = $purchase->item->user_id; // Itemが user_id を持つ前提（違うなら合わせる）
        $buyerId  = $purchase->buyer_id;

        $rateeId = ($raterId === $buyerId) ? $sellerId : $buyerId;

        // 同一取引で同一評価者は1回まで（DB unique + ここでも保険）
        Rating::updateOrCreate(
            ['purchase_id' => $purchase->id, 'rater_id' => $raterId],
            ['ratee_id' => $rateeId, 'score' => $validated['score']]
        );

        return redirect()->route('transactions.show', $purchase);
    }

    private function authorizePurchase(Purchase $purchase): void
    {
        $userId = Auth::id();
        $sellerId = $purchase->item->user_id; // ここもあなたのItemに合わせる

        abort_unless(
            $purchase->buyer_id === $userId || $sellerId === $userId,
            403
        );
    }

}
