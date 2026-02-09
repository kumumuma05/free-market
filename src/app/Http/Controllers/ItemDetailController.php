<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class ItemDetailController extends Controller
{
    /**
     * 商品詳細画面表示
     */
    public function show($item_id)
    {
        $item = Item::with('categories')->findOrFail($item_id);
        $likeCount = $item->likedUsers()->count();
        $commentCount = $item->comments()->count();
        $labels = Item::CONDITION;
        $isLiked = Auth::check()
            ? Auth::user()->likedItems()->where('item_id', $item->id)->exists()
            : false;

        return view('item.show', compact('item', 'likeCount', 'commentCount', 'labels', 'isLiked'));
    }

    /**
     * 'いいね'機能管理
     */
    public function like(Item $item)
    {
        if (!Auth::check()) {
            return back();
        }

        $user = Auth::user();

        $already = $user->likedItems()->where('item_id', $item->id)->exists();

        if ($already) {
            $user->likedItems()->detach($item->id);
        } else {
            $user->likedItems()->attach($item->id);
        }

        return back();
    }

    /**
     * コメント登録
     */
    public function store(CommentRequest $request, Item $item)
    {
        if (!Auth::check()) {
            return back();
        }

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'body' => $request->body,
        ]);

        return back();
    }
}
