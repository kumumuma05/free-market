<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;
use App\Models\Message;
use App\Models\Rating;

class Purchase extends Model
{
    use HasFactory;

    /**
     * 一括代入可能カラム
     */
    protected $fillable = [
        'item_id',
        'buyer_id',
        'payment_method',
        'shipping_postal',
        'shipping_address',
        'shipping_building',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * 商品の支払方法を表す定数
     * - DB上では数値で保持し、画面上は対応するラベルを表示する。
     */
    public const PAYMENT = [
        1 => 'コンビニ払い',
        2 => 'カード支払い',
    ];

    /**
     * 取引ステータス
     */
    public const STATUS_TRADING   = 'trading';
    public const STATUS_COMPLETED = 'completed';

    /**
     * 購入レコード作成時の自動処理
     * - 対応する商品のis_soldカラムをtrue（売却済み）に更新する
     */
    protected static function booted()
    {
        static::created(function ($purchase) {
            $purchase->item->update(['is_sold' => true]);
        });
    }

    /**
     * この取引に対する商品情報を取得する
     * - purchases.item_id -> items.id
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * この取引に対する買い手の情報を取得する
     * - purchases.buyer_id -> users.id
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * この取引に紐づくメッセージ一覧を取得
     * - purchases.id -> messages.purchase_id
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * この取引に紐づく評価一覧を取得
     * - purchases.id -> ratings.purchase_id
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * 取引が「進行中」か判定する
     */
    public function isTrading(): bool
    {
        return $this->status === self::STATUS_TRADING;
    }

    /**
     * 取引が「完了」状態かどうかを判定する
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}