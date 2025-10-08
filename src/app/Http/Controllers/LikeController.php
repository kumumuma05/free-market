<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;


class LikeController extends Controller
{
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
}
