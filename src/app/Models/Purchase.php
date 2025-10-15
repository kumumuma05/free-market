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

    public const PAYMENT = [
        1 => 'コンビニ払い',
        2 => 'カード支払い',
    ];

    protected static function booted(){
        static::created(function ($purchase) {
            $purchase->item->update(['is_sold' => true]);
        });
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}