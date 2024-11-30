<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'categories',
        'condition',
        'name',
        'description',
        'price',
    ];

    // カテゴリーを配列として扱えるように設定
    protected $casts = [
        'categories' => 'array',
    ];

    public function buyers()
    {
        return $this->belongsToMany(User::class, 'purchases', 'product_id', 'user_id')->withTimestamps();
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

}
