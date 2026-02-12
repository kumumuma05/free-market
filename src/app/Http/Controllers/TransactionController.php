<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Message;
use App\Models\Rating;
use App\Http\Requests\MessageRequest;
use App\Mail\TransactionCompletedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;


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
            ->oldest()
            ->get();

        // 評価モーダルの表示
        $myRating = $purchase->ratings()
            ->where('rater_id', Auth::id())
            ->first();
        $showRatingModal = $purchase->status === Purchase::STATUS_WAITING_RATING && empty($myRating);

        $userId = auth()->id();
        $isBuyer = ($purchase->buyer_id === $userId);
        $isSeller = ($purchase->item->user_id === $userId);

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

        return view('transaction.show', compact('purchase', 'messages', 'myRating', 'isBuyer', 'isSeller', 'editingMessage', 'showRatingModal', 'sidebarPurchases'));
    }

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

        return back();
    }

    /**
     * メッセージ編集画面表示(同じチャット画面を編集モードで表示)
     */
    public function editInline(Purchase $purchase, Message $message)
    {
    // アクセス制限
    $this->authorizePurchase($purchase);

    // この取引のメッセージか確認
    if ($message->purchase_id !== $purchase->id) {
        abort(404);
    }

    // 自分の投稿のみ編集可
    if ($message->sender_id !== Auth::id()) {
        abort(403);
    }

    $messages = $purchase->messages()->oldest()->get();

    $isBuyer = auth()->id() === $purchase->buyer_id;
    $isSeller = auth()->id() === $purchase->item->user_id;
    $myRating = $purchase->ratings()->where('rater_id', auth()->id())->first();

    $editingMessage = $message;

    return view('transaction.show', compact('purchase', 'messages', 'myRating', 'isBuyer', 'isSeller', 'editingMessage'));
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
