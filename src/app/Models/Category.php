<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Category extends Model
{
    use HasFactory;

    // 一括代入可能カラム
    protected $fillable = ['name',];

    /**
     * このカテゴリに属する商品の一覧を取得
     * - 中間テーブルcategory_items(category_id, item_id)を介してitemsに接続
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_items')->withTimestamps();
    }

}
