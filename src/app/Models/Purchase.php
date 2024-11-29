<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'payment_method',
    ];

    /**
     * 購入された商品の情報
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 購入したユーザーの情報
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
