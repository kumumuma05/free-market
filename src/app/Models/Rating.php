<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;
use App\Models\User;

class Rating extends Model
{
    use HasFactory;

    /**
     * 一括代入可能カラム
     */
    protected $fillable = [
        'purchase_id',
        'rater_id',
        'ratee_id',
        'score',
    ];

    /**
     * この評価に対する取引の情報を取得する
     * - ratings.purchase_id -> purchases.id
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * この評価に対する評価者の情報を取得する
     * - ratings.rater_id -> users.id
     */
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    /**
     * この評価に対する評価対象者の情報を取得する
     * - ratings.ratee_id -> users.id
     */
    public function ratee()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }
}
