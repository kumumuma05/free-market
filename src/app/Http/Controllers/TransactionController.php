<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Mail\TransactionCompletedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * 取引チャット画面表示
     */
    public function show(Purchase $purchase)
    {
        // アクセス制限（購入者と出品者のみ）
        $this->authorizePurchase($purchase);

        // メッセージ既読トリガー
        $purchase->load('item');
        $userId = auth()->id();
        $isBuyer = ($purchase->buyer_id === $userId);
        $isSeller = ($purchase->item->user_id === $userId);

        $lastReadAt = $isBuyer ? $purchase->buyer_last_read_at : $purchase->seller_last_read_at;

        $query = $purchase->messages()
            ->where('sender_id', '!=', $userId);

        if ($lastReadAt !== null) {
            $query->where('created_at', '>', $lastReadAt);
        }

        $hasUnread = $query->exists();

        if ($hasUnread) {
            $now = now();
            if ($isBuyer) {
                $purchase->update(['buyer_last_read_at' => $now]);
            } else {
                $purchase->update(['seller_last_read_at' => $now]);
            }
        }

        // メッセージの表示
        $messages = $purchase->messages()
            ->with('sender')
            ->oldest()
            ->get();

        // 評価モーダルの表示
        $myRating = $purchase->ratings()
            ->where('rater_id', Auth::id())
            ->first();
        $showRatingModal = $purchase->status === Purchase::STATUS_WAITING_RATING && empty($myRating);

        // その他の取引表示（左サイドバー用）
        $sidebarPurchases = Purchase::query()
            ->where('status', Purchase::STATUS_TRADING)
            ->where(function ($query) use ($userId) {
                $query->where('buyer_id', $userId)
                    ->orWhereHas('item', function($itemQuery) use ($userId) {
                        $itemQuery->where('user_id', $userId);
                    });
            })
            ->whereKeyNot($purchase->id)
            ->with('item')
            ->withMax('messages', 'created_at')
            ->orderByRaw('COALESCE(messages_max_created_at, purchases.created_at) DESC')
            ->get();

        $editingMessage = null;

        // セッション表示用
        $draftBody = session($this->draftKey($purchase->id), '');

        return view('transaction.show', compact('purchase', 'messages', 'myRating', 'isBuyer', 'isSeller', 'editingMessage', 'showRatingModal', 'sidebarPurchases', 'draftBody'));
    }

    /**
     * 未送信メッセージをセッションに保存し、別取引のチャットへ遷移
     */
    public function switch(Request $request, Purchase $purchase)
    {
        // 現在の取引にアクセスできる人だけ保存を許可
        $this->authorizePurchase($purchase);

        $validated = $request->validate([
            'to_purchase_id' => ['required', 'integer', 'exists:purchases,id'],
            'body' => ['nullable', 'string'],
        ]);

        // 現在取引の下書きをセッション保存
        session([$this->draftKey($purchase->id) => (string) ($validated['body'] ?? '')]);

        // 遷移先の取引もアクセス権チェック（覗き見防止で必須）
        $toPurchase = Purchase::findOrFail((int) $validated['to_purchase_id']);
        $this->authorizePurchase($toPurchase);

        return redirect("/transaction/{$toPurchase->id}");
    }

    /**
     * 取引完了判断
     */
    public function complete(Purchase $purchase)
    {
        // アクセス制限
        $this->authorizePurchase($purchase);
        if ($purchase->buyer_id !== Auth::id()) {
            abort(403);
        }

        // すでに完了なら何もしない（2度押し対策）
        if ($purchase->isCompleted()) {
            return back();
        }

        // 取引完了処理
        $purchase->update([
            'status' => Purchase::STATUS_WAITING_RATING,
        ]);
        // メール送信
        $purchase->load(['item.seller', 'buyer']);
        $sellerEmail = $purchase->item->seller->email;

        Mail::to($sellerEmail)->send(new TransactionCompletedMail($purchase));

        return redirect("/transaction/{$purchase->id}")
        ->with('show_rating_modal', true);
    }

    /**
     * 「購入者」と「出品者」のみが操作できるように制限
     */
    private function authorizePurchase(Purchase $purchase) :void
    {
        $purchase->loadMissing('item');
        $userId = Auth::id();
        $sellerId = $purchase->item->user_id;

        if ($purchase->buyer_id !== $userId && $sellerId !== $userId) {
            abort(403);
        }
    }

    /**
     * 取引ごとの下書き保存用のセッションキーを生成
     */
    private function draftKey(int $purchaseId): string
    {
        return "draft.transaction.{$purchaseId}.body";
    }
}
