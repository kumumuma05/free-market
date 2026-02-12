<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating;
use App\Models\Purchase;


class RatingController extends Controller
{
    public function store(Request $request, Purchase $purchase)
    {
        // アクセス制限
        $this->authorizePurchase($purchase);

        // 評価待ちの時だけ評価可能
        if (! $purchase->isWaitingRating()) {
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

        $sellerId = $purchase->item->seller_id;
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

        // 両者の評価が終了後取引終了登録
        $ratingsCount = $purchase->ratings()->count();
        if ($ratingsCount >=2) {
            $purchase->update([
                'status' => Purchase::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        }

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
