<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    // 一括代入可能カラム
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'postal',
        'address',
        'building',
        'profile_completed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'profile_completed' => 'bool',
    ];

    /**
     * プロフィール画像のURLを取得するアクセサ
     *
     * 優先順位
     * 1. セッションに一時保存された画像
     * 2. profile_imageに登録された画像
     *  - 'images/'で始まるパスが優先
     * 3. 上記以外はデフォルト画像
     */
    public function getProfileImageUrlAttribute()
    {
        if (session()->has('temp_image')) {
            return Storage::url(session('temp_image'));
        }

        if (!empty($this->profile_image)) {
            if (Str::startsWith($this->profile_image, 'images/')) {
                return asset($this->profile_image);
            }
            return Storage::url($this->profile_image);
        }

        return asset('images/profile_image/default.png');
    }

    /**
     * このユーザーが出品した商品の一覧を取得
     * - users.id -> items.user_id
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * このユーザーが投稿したコメント一覧を取得
     * - users.id -> comments.user_id
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * このユーザーが「いいね」した商品一覧を取得
     * - 中間テーブルlikes（user_id, item_id）を介してitemsに接続
     */
    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes')->withTimestamps();
    }

    /**
     * このユーザーが購入した取引一覧を取得
     * - user.id -> purchases.buyer_id
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }

    /**
     * このユーザーが購入した商品の一覧を取得
     * - 中間テーブルpurchases（buyer_id, item_id）を介してitemsに接続
     */
    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'purchases', 'buyer_id', 'item_id')->withTimestamps();
    }
}
