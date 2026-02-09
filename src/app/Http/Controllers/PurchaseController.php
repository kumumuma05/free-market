<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;
use Stripe\StripeClient;

class PurchaseController extends Controller
{
    /**
     * 商品購入画面表示
     */
    public function show(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 配送先（セッションがあれば優先）
        $shipAddress = $request->session()->get("changed_address.$item_id", []);
        $shipping = [
            'shipping_postal' => $shipAddress['shipping_postal'] ?? ($user->postal),
            'shipping_address' => $shipAddress['shipping_address'] ?? ($user->address),
            'shipping_building' => $shipAddress['shipping_building'] ?? ($user->building),
        ];

        if ($request->has('payment_method')) {
            $request->session()->put("payment_method.$item_id", $request->query('payment_method'));
        }

        $payment = (int)$request->session()->get("payment_method.$item_id", 0);

        return view('purchase.checkout', compact('item', 'user', 'shipping', 'payment'));
    }

    /**
     * 住所変更ページ表示
     */
    public function shippingShow($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        return view('purchase.address', compact('item', 'user'));
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
     * 購入情報登録（コンビニ払い選択時）
     */
    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);
        if (!Auth::check()) {
            return back();
        }

        $sesAddress = $request->session()->get("changed_address.$item_id", []);
        $postal = $sesAddress['shipping_postal'] ?? $request->input('shipping_postal');
        $address = $sesAddress['shipping_address'] ?? $request->input('shipping_address');
        $building = $sesAddress['shipping_building'] ?? $request->input('shipping_building', '');

        $payment = (int)($request->session()->get("payment_method.$item_id", 0));

        // 自分の商品は購入不可
        if ($item->user_id === Auth::id()) {
            return back();
        }

        // 購入済み商品を弾く
        if (Purchase::where('item_id', $item->id)->exists()) {
            return back();
        }

        // カード決済フローへ
        if ($payment === 2) {
            return $this->startStripeCheckout($item, [
                'shipping_postal' => $postal,
                'shipping_address' => $address,
                'shipping_building'=> $building,
            ]);
        }

        // コンビニ支払い時のDB登録
        Purchase::create([
            'item_id' => $item->id,
            'buyer_id' => Auth::id(),
            'payment_method' => 1,
            'shipping_postal' => $postal,
            'shipping_address' => $address,
            'shipping_building' => $building,
        ]);

        // セッションクリア
        $request->session()->forget("changed_address.$item_id");
        $request->session()->forget("payment_method.$item_id");

        return redirect('/')->with('status', '商品の購入が完了しました。');
    }

    /**
     * カード支払い選択時のstripe使用機能設定
     */
    private function startStripeCheckout(Item  $item, array $shipping)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        // Checkoutセッション新規作成
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => Auth::user()->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => (int)$item->price
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'item_id' => (string)$item->id,
                'buyer_id' => (string)Auth::id(),
                'shipping_postal' => $shipping['shipping_postal'] ?? '',
                'shipping_address'  => $shipping['shipping_address'] ?? '',
                'shipping_building' => $shipping['shipping_building'] ?? '',
            ],
            'success_url' => url("/purchase/{$item->id}/success?session_id={CHECKOUT_SESSION_ID}"),
            'cancel_url'  => url("/purchase/{$item->id}/cancel"),
        ]);

        return redirect()->away($session->url);
    }

    /**
     * 購入情報登録（カード支払い選択・成功時）
     */
    public function success(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        if (! Auth::check()) {
            return redirect("/login");
        }

        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect("/purchase/{$item->id}");
        }

        // 決済情報の取得
        $stripe  = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId, ['expand' => ['payment_intent']]);

        // 支払が成功しているか確認（失敗しているときは購入画面へリダイレクト）
        $sessionPaid = $session->payment_status === 'paid';
        $intentSucceeded = $session->payment_intent->status === 'succeeded';
        $paid = $session && $sessionPaid && $intentSucceeded;

        if (!$paid) {
            return redirect("/purchase/{$item->id}");
        }

        // 二重購入防止
        if (Purchase::where('item_id', $item->id)->exists()) {
            return redirect("/item/{$item->id}");
        }

        // カード支払い時のDB登録
        $metadata = $session->metadata;

        Purchase::create([
            'item_id'           => $item->id,
            'buyer_id'          => (int)$metadata->buyer_id,
            'payment_method'    => 2,
            'shipping_postal'   => (string)$metadata->shipping_postal,
            'shipping_address'  => (string)$metadata->shipping_address,
            'shipping_building' => (string)$metadata->shipping_building,
        ]);

        // セッション情報リセット
        $request->session()->forget("changed_address.$item_id");
        $request->session()->forget("payment_method.$item_id");

        return redirect('/')->with('status', '商品の購入が完了しました。');
    }

    /**
     * 失敗（キャンセル）時の戻り先
     */
    public function cancel($item_id)
    {
        return redirect("/purchase/{$item_id}");
    }
}
