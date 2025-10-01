<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;


class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $activeTab = 'rec';
        if($tab === 'mylist' && Auth::check())
        {
            $items = Auth::user()->likedItems()->latest()->get();
            $activeTab = 'mylist';
        } else {
            $query = Item::query()->latest();
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }
            $items = $query->get();
        }

        return view('item.index', compact('items', 'activeTab'));
    }

    public function search(Request $request)
    {
        $items = Item::KeywordSearch($request->keyword)->get();
        $activeTab = 'recommend';

        return view('item.index', compact('items', 'activeTab'));
    }
}