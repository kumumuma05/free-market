<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function order($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('/order', compact('item',));
    }
}
