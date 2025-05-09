<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\TradingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'rated_id' => 'required|exists:users,id', // 評価対象のユーザーID（出品者または購入者）
            'product_id' => 'required|exists:products,id', // 商品ID
        ]);

        // ログインユーザーが評価者
        $rater_id = Auth::id();  // 評価をするユーザー（出品者）
        $rated_id = $request->rated_id;  // 評価されるユーザー（購入者）
        $product_id = $request->product_id;  // 商品ID

        // すでに評価されているか確認
        $existingRating = Rating::where('rater_id', $rater_id)
                                ->where('rated_id', $rated_id)
                                ->where('product_id', $product_id)
                                ->first();

        if ($existingRating) {
            // すでに評価がされている場合
            return redirect()->route('chat.show', ['product_id' => $product_id])
                             ->with('error', 'すでに評価済みです。');
        }

        // 新しい評価を作成
        $rating = new Rating();
        $rating->rater_id = $rater_id;
        $rating->rated_id = $rated_id;
        $rating->score = $request->score;
        $rating->product_id = $product_id;
        $rating->save();  // 評価を保存

        // 取引ステータスを「取引終了」に更新
        $tradingProduct = TradingProduct::where('product_id', $product_id)
                                        ->where('user_id', $rater_id)
                                        ->first();

        if ($tradingProduct) {
            $tradingProduct->status = '取引完了';  // 取引完了に変更
            $tradingProduct->save();
        }

        return redirect()->route('chat.show', ['product_id' => $product_id])
                         ->with('success', '評価が完了しました');
    }
}