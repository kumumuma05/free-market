<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemDetailController extends Controller
{

    public function show($item_id) {
        $item = Item::findOrFail($item_id);
        $likeCount = $item->likedUsers()->count();
        $commentCount = $item->comments()->count();
        $item->load('categories');
        $labels = Item::CONDITION;
        $isLiked = Auth::check()
            ? Auth::user()->likedItems()->where('item_id', $item->id)->exists()
            : false;

        return view('item.show', compact('item', 'likeCount', 'commentCount', 'labels', 'isLiked'));
    }
}
