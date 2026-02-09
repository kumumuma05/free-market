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
    public function store(Request $request, Purchase $purchase)
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
            'body' => $body,
            'image_path' => $imagePath,
        ]);

        return redirect();
    }
}
