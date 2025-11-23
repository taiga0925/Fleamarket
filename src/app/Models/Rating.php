<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'rater_id',
        'rating',
        'comment',
    ];

    // 評価されたユーザー（出品者）
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 評価したユーザー（購入者）
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    // 対象商品
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
