<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Item;

class SellController extends Controller
{
    public function sell(Request $request)
    {
        $categories = Category::all();
        $labels = Item::CONDITION;
        $items = Item::all();
        return view('sell', compact('categories', 'items', 'labels'));
    }
}
