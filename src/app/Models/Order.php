<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'status',
        'subtotal',
        'shipping_fee',
        'total',
        'carrier',
        'tracking_no',
        'shipped_at',
        'delivered_at'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
