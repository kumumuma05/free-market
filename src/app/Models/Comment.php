<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    /**
     * 一括代入可能カラム
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'body',
    ];

    /**
     * このコメントが属する商品情報を取得
     * - comments.item_id -> items.id
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * このコメントが属するユーザー情報を取得
     * - comments.user_id -> users.id
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
