<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;

class Like extends Model
{
    use HasFactory;

    /**
     * 一括代入可能カラム
     */
    protected $fillable = [
        'user_id',
        'item_id',
    ];

    /**
     * この「いいね」をしたユーザー情報を取得
     * - likes.user_id -> users.id
     * - 中間テーブル（多対多）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この「いいね」を付けられた商品情報を取得
     * ‐ likes.item_id -> items.id
     * - 中間テーブル（多対多）
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
