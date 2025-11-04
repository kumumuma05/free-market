<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        // バリデーションチェック（仕様に準じる）
        $request->validate([
            'image_path' => [
                'required', 'file', 'image', 'mimes:jpeg,png',
            ]
        ]);

        // ユーザー専用のセッションファイル保存用ディレクトリを作成
        $userDir = 'tmp/item_image/' . auth()->id();
        Storage::disk('public')->deleteDirectory($userDir);
        Storage::disk('public')->makeDirectory($userDir);

        // セッションファイル保存
        $extension = $request->file('image_path')->extension();
        $name = 'current.' . $extension;
        $path = $request->file('image_path')->storeAs($userDir, $name, 'public');

        $request->session()->put('temp_image', $path);

        return redirect('/sell');
    }

    /**
     * 出品商品の登録
     */
    public function store(ExhibitionRequest $request)
    {
        $tempImage = $request->session()->get('temp_image');

        if (!$tempImage || !Storage::disk('public')->exists($tempImage)) {
            return back()->withInput();
        }

        // 商品画像のstorageへの本登録
        $extension = pathinfo($tempImage, PATHINFO_EXTENSION) ?: 'jpg';
        $destDir ='item_image/' . auth()->id();
        $dest = $destDir . '/' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->makeDirectory($destDir);
        Storage::disk('public')->move($tempImage, $dest);

        $item = Item::create([
            'user_id' => auth()->id(),
            'product_name' => $request->product_name,
            'brand'=> $request->brand,
            'description' => $request->description,
            'price' => $request->price,
            'condition' => $request->condition,
            'image_path' => $dest,
        ]);

        $item->categories()->sync($request->category_ids);

        $request->session()->forget('temp_image');
        Storage::disk('public')->deleteDirectory('tmp/item_image/' . auth()->id());

        return redirect('/mypage')->with('status', '商品を出品しました');
    }
}
