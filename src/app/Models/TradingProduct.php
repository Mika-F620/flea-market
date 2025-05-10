<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'user_id', 
        'name', 
        'image', 
        'price', 
        'status',
        'seller_id',
        'buyer_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function trading($id)
{
    // 商品を取得
    $product = Product::findOrFail($id);

    // ログインしているユーザー（購入者）を取得
    $buyer = Auth::user();

    // 出品者を取得
    $seller = $product->user;

    // 出品者と購入者の両方の取引情報を保存
    TradingProduct::create([
        'product_id' => $product->id,  // 商品ID
        'user_id' => $seller->id,      // 出品者ID
        'name' => $product->name,      // 商品名
        'image' => $product->image,    // 商品画像
        'price' => $product->price,    // 価格
        'status' => '取引中',          // 取引中の状態
    ]);

    TradingProduct::create([
        'product_id' => $product->id,  // 商品ID
        'user_id' => $buyer->id,       // 購入者ID
        'name' => $product->name,      // 商品名
        'image' => $product->image,    // 商品画像
        'price' => $product->price,    // 価格
        'status' => '取引中',          // 取引中の状態
    ]);

    // チャット画面に遷移
    return redirect()->route('chat.show', ['product_id' => $product->id]);
}





}
