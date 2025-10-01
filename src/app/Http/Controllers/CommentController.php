<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        if (!Auth::check()){
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