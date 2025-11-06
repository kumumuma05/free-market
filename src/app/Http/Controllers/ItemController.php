<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;


class ItemController extends Controller
{

    /**
     * 商品一覧画面表示
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $activeTab = $request->query('tab') ?: 'recommend';
        $keyword = $request->query('keyword', '');

        if($tab === 'mylist') {
            if(Auth::check()) {
                // ログインユーザーはいいね済みだけ表示
                $query = Auth::user()->likedItems()->latest();
                if($keyword !== '') {
                    $query = $query->keywordSearch($keyword);
                }
                $items = $query->get();
            } else {
                // ビジターは空リストを返す
                $items = collect();
            }
        } else {

            $query = Item::query()->latest();

            if(Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            if($keyword !== '') {
                $query = $query->keywordSearch($keyword);
            }

            $items = $query->get();
        }

        return view('item.index', compact('items', 'activeTab', 'keyword'));
    }

    /**
     * 商品検索
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->query('keyword', '');
        $tab = $request->query('tab', 'recommend');

        if($user && $tab === 'mylist') {
            $query = $user->likedItems()->latest();
        } elseif ($user) {
            $query = Item::query()->latest()->where('user_id', '!=', $user->id);
        } else {
            $query = Item::query()->latest();
            $tab = 'recommend';
        }

        if ($keyword !== '') {
            $query->keywordSearch($keyword);
        }

        $items = $query->get();
        $activeTab = $tab;

        return view('item.index', compact('items', 'keyword', 'activeTab'));
    }
}