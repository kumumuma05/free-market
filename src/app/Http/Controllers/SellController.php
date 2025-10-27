<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;

class SellController extends Controller
{

    /**
     * 商品出品画面表示
     */
    public function create(Request $request)
    {

        $categories = Category::all();
        $labels = Item::CONDITION;
        $items = Item::all();

        $image = $request->session()->get('temp_image');

        return view('sell', compact('categories', 'items', 'labels', 'image'));
    }

    /**
     * 商品画像のセッションアップロード
     */
    public function imagePostSession(Request $request)
    {

        if ($old = $request->session()->get('temp_image')) {
            storage::disk('public')->delete($old);
        }

        $path = $request->file('image_path')->store('item_image', 'public');

        $request->session()->put('temp_image', $path);

        return redirect('/sell');
    }

    /**
     * 出品商品の登録
     */
    public function store(ExhibitionRequest $request)
    {
        $image = $request->session()->get('temp_image');

        $item = Item::create([
            'user_id' => auth()->id(),
            'product_name' => $request->product_name,
            'brand'=> $request->brand, 'description' => $request->description,
            'price' => $request->price, 'condition' => $request->condition,
            'image_path' => $image]);

        $item->categories()->sync($request->category_ids);

        $request->session()->forget('temp_image');

        return redirect('/sell');
    }
}
