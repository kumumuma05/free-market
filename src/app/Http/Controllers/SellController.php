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
        return view('sell', compact('categories', 'items', 'labels'));
    }

    /**
     * 出品商品の登録
     */
    public function store(ExhibitionRequest $request)
    {
        $path = $request->file('image_path')->store('item_image', 'public');

        $item = Item::create([
            'user_id' => auth()->id(),
            'product_name' => $request->product_name,
            'brand'=> $request->brand, 'description' => $request->description,
            'price' => $request->price, 'condition' => $request->condition, 'image_path' => $path]);

        $item->categories()->sync($request->category_ids);

        return redirect()->back()->withInput()->with('temp_image', $path );
    }
}
