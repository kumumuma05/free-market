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

        $shipAddress = $request->session()->get("changed_address.$item_id", []);
        $shipping = [
        'shipping_postal' => $shipAddress['shipping_postal'] ?? ($user->postal),
        'shipping_address' => $shipAddress['shipping_address'] ?? ($user->address),
        'shipping_building' => $shipAddress['shipping_building'] ?? ($user->building),
    ];

        if ($request->has('payment_method')) {
            $request->session()->put("payment_method.$item_id", $request->query('payment_method'));
        }

        $payment = $request->session()->get("payment_method.$item_id");
        // dd(session()->all());

        return view('purchase', compact('item','user', 'shipping', 'payment'));
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
     * 配送先住所変更実行（商品ごとにセッションへ一時保存）
     */
    public function shippingUpdate(AddressRequest $request, $item_id)
    {
        $changed = $request->validated();

        $request->session()->put("changed_address.$item_id", [
            'shipping_postal' => $changed['shipping_postal'],
            'shipping_address' => $changed['shipping_address'],
            'shipping_building' => $changed['shipping_building'] ?? '',
        ]);

        return redirect("/purchase/{$item_id}");
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

        $sesAddress = $request->session()->get("changed_address.$item_id", []);
        $postal = $sesAddress['shipping_postal'] ?? $request->input('shipping_postal');
        $address = $sesAddress['shipping_address'] ?? $request->input('shipping_address');
        $building = $sesAddress['shipping_building'] ?? $request->input('shipping_building', '');

        $payment = $request->session()->get("payment_method.$item_id", $request->input('payment_method', ''));

        Purchase::create([
            'item_id' =>$item->id,
            'buyer_id' =>Auth::id(),
            'payment_method' => $payment,
            'shipping_postal' => $postal,
            'shipping_address' => $address,
            'shipping_building' => $building,
        ]);

        $request->session()->forget("changed_address.$item_id");
        $request->session()->forget("payment_method.$item_id");

        return redirect('/');
    }


}
