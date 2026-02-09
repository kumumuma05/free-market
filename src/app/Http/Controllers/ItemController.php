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
        $activeTab = $tab;
        $keyword = $request->query('keyword', '');

        // マイリストタブ選択時
        if ($tab === 'mylist') {
            if (Auth::check()) {
                // ログインユーザーはいいね済みだけ表示(キーワード検索もマイリストに持ち越し)
                $user = Auth::user();
                $query = $user->likedItems()->where('items.user_id', '!=', $user->id)->latest();

                if ($keyword !== '') {
                    $query = $query->keywordSearch($keyword);
                }

                $items = $query->get();
            } else {
                // ビジターは空リストを返す
                $items = collect();
            }
        } else {
            // おすすめタブ選択時
            $query = Item::query()->latest();

            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            if ($keyword !== '') {
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

        // マイリスト選択時（ログイン中）
        if ($user && $tab === 'mylist') {
            // いいね済み商品から検索（自分が出品した商品以外）
            $query = $user->likedItems()->where('items.user_id', '!=', $user->id)->latest();

        // おすすめ選択時（ログイン中）
        } elseif ($user) {
            // 全体から検索（自分が出品した商品以外）
            $query = Item::query()->latest()->where('user_id', '!=', $user->id);

        // 未ログイン者の場合
        } else {
            $query = Item::query()->latest();
            $tab = 'recommend';
        }

        // キーワード検索
        if ($keyword !== '') {
            $query = $query->keywordSearch($keyword);
        }

        $items = $query->get();
        $activeTab = $tab;

        return view('item.index', compact('items', 'keyword', 'activeTab'));
    }
}