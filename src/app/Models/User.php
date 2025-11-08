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
     * プロフィールのデフォルト画像呼び出しアクセサ
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
     * itemsテーブルとのリレーション(1対N（0～多)）
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * commentsテーブルとのリレーション(1対N（0～多)）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * itemsテーブルとのリレーション(n対n:0～多)）
     * 中間テーブル:likes
     */
    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes')->withTimestamps();
    }

    /**
     * purchasesテーブルとのリレーション(1対N（0～多)）
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }

    /**
     * 購入した商品一覧を取得（purchases中間テーブル経由）
     */
    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'purchases', 'buyer_id', 'item_id')->withTimestamps();
    }
}
