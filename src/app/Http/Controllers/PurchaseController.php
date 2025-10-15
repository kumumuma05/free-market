<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;



class PurchaseController extends Controller
{

    /**
     * 商品購入画面表示
     */
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        return view('purchase', compact('item','user'));
    }

    /**
     * 購入情報登録
     */
    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        if (!Auth::check()){
            return back();
        }

        Purchase::create([
            'item_id' =>$item->id,
            'buyer_id' =>Auth::id(),
            'payment_method' => $request->payment_method,
            'shipping_postal' => $request->shipping_postal,
            'shipping_address' => $request->shipping_address,
            'shipping_building' => $request->shipping_building,
        ]);

        return redirect('/');
    }

    /**
     * 住所変更ページ表示
     */
    public function shippingshow($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        return view('address', compact('item','user'));
    }

    /**
     * 配送先住所変更実行
     */
    public function shippingupdate($item_id)
    {

    }
}
