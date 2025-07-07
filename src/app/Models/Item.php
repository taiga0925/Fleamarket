<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'image',
        'status',
        'item',
        'brand',
        'detail',
        'money',
    ];

    protected $guarded = [
        'id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likeUsers()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function soldToUsers()
    {
        return $this->belongsToMany(User::class, 'sold_items');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function shops() {
        return $this->belongsToMany(Shop::class,'shop_items');
    }
}
