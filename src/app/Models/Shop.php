<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'img_url',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'shop_staff')
            ->withPivot('id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'shop_items');
    }
}
