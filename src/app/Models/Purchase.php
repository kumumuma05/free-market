<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'payment_method',
        'shipping_postal',
        'shipping_address',
        'shipping_building',
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
}