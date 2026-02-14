<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Message;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * メッセージの登録（送信）
     */
    public function store(MessageRequest $request, Purchase $purchase)
    {
        // アクセス制限（購入者と出品者のみ）
        $this->authorizePurchase($purchase);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        Message::create([
            'purchase_id' => $purchase->id,
            'sender_id' => Auth::id(),
            'body' => $request->body,
            'image_path' => $imagePath,
        ]);

        // 送信成功なので下書きを削除
        session()->forget("draft.transaction.{$purchase->id}.body");

        return back();
    }

    /**
     * メッセージ編集画面表示(同じチャット画面を編集モードで表示)
     */
    public function editInline(Purchase $purchase, Message $message)
    {
        // アクセス制限
        $this->authorizePurchase($purchase);
        $this->authorizeMessage($purchase, $message);

        // 自分の投稿のみ編集可
        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        // Bladeで必要なリレーションを揃える
        $purchase->loadMissing(['item.seller', 'buyer']);

        $userId = Auth::id();
        $isBuyer  = ($purchase->buyer_id === $userId);
        $isSeller = ($purchase->item->user_id === $userId);

        // メッセージ表示
        $messages = $purchase->messages()->with('sender')->oldest()->get();

        // 評価モーダル用
        $myRating = $purchase->ratings()->where('rater_id', $userId)->first();

        $showRatingModal = ($purchase->status === Purchase::STATUS_WAITING_RATING) && empty($myRating);

        // サイドバー用（show() と同じ）
        $sidebarPurchases = Purchase::query()
            ->where('status', Purchase::STATUS_TRADING)
            ->where(function ($query) use ($userId) {
                $query->where('buyer_id', $userId)
                    ->orWhereHas('item', function ($itemQuery) use ($userId) {
                        $itemQuery->where('user_id', $userId);
                    });
            })
            ->whereKeyNot($purchase->id)
            ->with('item')
            ->withMax('messages', 'created_at')
            ->orderByRaw('COALESCE(messages_max_created_at, purchases.created_at) DESC')
            ->get();

        $editingMessage = $message;

        $draftBody = session("draft.transaction.{$purchase->id}.body", '');

        return view('transaction.show', compact('purchase', 'messages', 'myRating', 'isBuyer', 'isSeller', 'editingMessage', 'showRatingModal', 'sidebarPurchases', 'draftBody'));
    }

    /**
     * メッセージ編集
     */
    public function update(MessageRequest $request, Purchase $purchase, Message $message)
    {
        // アクセス制限
        $this->authorizePurchase($purchase);
        $this->authorizeMessage($purchase, $message);

        // 自分の投稿のみ編集可
        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        $update = [
            'body' => $request->body,
        ];

        // 画像編集
        if ($request->hasFile('image')) {
            if ($message->image_path) {
                Storage::disk('public')->delete($message->image_path);
            }
            $path = $request->file('image')->store('chat_images', 'public');
            $update['image_path'] = $path;
        }

        $message->update($update);

        return redirect("/transaction/{$purchase->id}");
    }

    /**
     * メッセージ削除
     */
    public function destroy(Purchase $purchase, Message $message)
    {
        // アクセス制限
        $this->authorizePurchase($purchase);
        $this->authorizeMessage($purchase, $message);

        // 自分の投稿だけ削除可
        if ($message->sender_id !== Auth::id()) {
            abort(403);
        }

        // 画像があればファイルも消す
        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }
        $message->delete();

        return back();
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
     * メッセージが指定の取引に属しているか確認
     */
    private function authorizeMessage(Purchase $purchase, Message $message): void
    {
        if ($message->purchase_id !== $purchase->id) {
            abort(404);
        }
    }
}
