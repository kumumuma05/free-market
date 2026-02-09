<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Purchase;
use App\Models\User;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'brand',
        'description',
        'price',
        'condition',
        'image_path',
        'is_sold',
    ];

    /**
     * 商品のコンディションを表す定数
     * - DB上では数値で保持し、画面上は対応するラベルを表示する。
     */
    public const CONDITION = [
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    ];

    /**
     * 商品名検索ローカルスコープ（キーワード部分検索可能）
     */
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('product_name', 'like', '%' . $keyword . '%');
        }

        return $query;
    }

    /**
     * 商品画像のURLを取得するアクセサ
     * - 外部URLの場合はそのまま返す
     * - ストレージ保存画像の場合はStorage::urlで変換
     */
    public function getImageUrlAttribute()
    {
        if (preg_match('#^https?://#', $this->image_path)) {
            return $this->image_path;
        }

        return Storage::url($this->image_path);
    }

    /**
     * この商品に対するコメント一覧を取得
     * - items.id -> comments.item_id
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * この商品に対する購入履歴を取得
     * - items.id -> purchases.item_id
     */
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    /**
     * この商品の出品者（ユーザー）情報を取得
     * - items.user_id -> users.id
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * この商品に「いいね」したユーザーの一覧を取得
     * - 中間テーブルlikes(item_id, user_id)を介してusersに接続
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * この商品に属するカテゴリの一覧を取得
     * - 中間テーブルcategory_items(item_id, category_id)を介してcategoriesに接続
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items')->withTimestamps();
    }
}
