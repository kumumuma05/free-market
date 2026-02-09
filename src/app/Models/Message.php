<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Purchase;
use App\Models\User;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 一括代入可能カラム
     */
    protected $fillable = [
        'purchase_id',
        'sender_id',
        'body',
        'image_path',
    ];

    /**
     * このメッセージに対する取引の情報を取得する
     * - messages.purchase_id -> purchases.id
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * このメッセージの送信者の情報を取得する
     * - messages.sender_id -> users.id
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
