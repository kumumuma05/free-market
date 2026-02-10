<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Message;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    /**
     * 取引チャット画面表示
     */
    public function show(Purchase $purchase)
    {
        // アクセス制限（購入者と出品者のみ）
        $this->authorizePurchase($purchase);

        // メッセージの表示
        $messages = $purchase->messages()
            ->with('sender')
            ->latest()
            ->get();

        // 評価状況（評価済みか）
        $myRating = $purchase->ratings()
            ->where('rater_id', Auth::id())
            ->first();

        $userId = auth()->id();
        $isBuyer = ($purchase->buyer_id === $userId);
        $isSeller = ($purchase->item->user_id === $userId);

        return view('transaction.show', compact('purchase', 'messages', 'myRating', 'isBuyer', 'isSeller'));
    }

    /**
     * メッセージの登録（送信）
     */
    public function store(MessageRequest $request, Purchase $purchase)
    {
        // アクセス制限（購入者と出品者のみ）
        $this->authorizePurchase($purchase);

        $imagePath = null;
        if ($request->hasFail('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
        }

        Message::create([
            'purchase_id' => $purchase->id,
            'sender_id' => Auth::id(),
            'body' => $request->body,
            'image_path' => $imagePath,
        ]);

        return back();
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
            $path = $request->file('image')->store('messages', 'public');
            $update['image_path'] = $path;
        }

        $message->update($update);

        return back();
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
        $message -> delete();

        return back();
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

        $purchase->update([
            'status' => Purchase::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        // メール送信
        $purchase->load(['item.user', 'buyer']);

        $sellerEmail = $purchase->item->user->email;
        Mail::to($sellerEmail)->send(new TransactionCompleteMail($purchase));

        return back();
    }

    /**
     * 「購入者」と「出品者」のみが操作できるように制限
     */
    private function authorizePurchase(Purchase $purchase) :void
    {
        $userId = Auth::id();
        $sellerId = $purchase->item->user_id;

        if ($purchase->buyer_id !== $userId && $sellerId !== $userId) {
            abort(403);
        }
    }

    /**
     * メッセージが指定の取引に属しているか確認
     */
    private function authorizeMessage(Purchase $purchase, Message $message): variant_mod{
        if ($message->purchase_id !== $purchase->id) {
            abort(404);
        }
    }
}
