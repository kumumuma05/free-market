<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;



class PurchaseController extends Controller
{

    /**
     * 商品購入画面表示
     */
    public function show(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $changed = session("changed_address.$item_id", []);
        $shipping = [
        'shipping_postal' => $changed['shipping_postal'] ?? ($user->postal),
        'shipping_address' => $changed['shipping_address'] ?? ($user->address),
        'shipping_building' => $changed['shipping_building'] ?? ($user->building),
    ];

        $payment = $request->query('payment_method', '');

        return view('purchase', compact('item','user', 'shipping', 'payment'));
    }

    /**
     * 住所の変更は商品ごとに行う
     */
    public function complete($item_id)
    {
        session()->forget("changed_address.$item_id");
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
    public function shippingupdate(AddressRequest $request, $item_id)
    {
        $changed = $request->validated();

        session([
            "changed_address.$item_id" => [
                'shipping_postal' => $changed['shipping_postal'],
                'shipping_address' => $changed['shipping_address'],
                'shipping_building' => $changed['shipping_building'] ?? '',
            ],
        ]);

        return redirect("/purchase/{$item_id}");
    }
}
