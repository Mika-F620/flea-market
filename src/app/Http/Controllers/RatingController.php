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
            'product_id' => 'required|exists:products,id',
        ]);

        $rater_id = Auth::id();  // 評価をするユーザー（ログイン中のユーザー）
        $product_id = $request->product_id;  // 商品ID

        // 商品に関連する取引情報を取得
        $tradingProduct = TradingProduct::where('product_id', $product_id)
                                        ->where(function ($query) use ($rater_id) {
                                            // 出品者または購入者
                                            $query->where('seller_id', $rater_id)
                                                ->orWhere('buyer_id', $rater_id);
                                        })
                                        ->first();

        if (!$tradingProduct) {
            return redirect()->route('chat.show', ['product_id' => $product_id])
                            ->with('error', '取引情報が見つかりません。');
        }

        // 評価対象ユーザー（相手）を決定
        $rated_id = ($rater_id === $tradingProduct->seller_id) 
                    ? $tradingProduct->buyer_id // 出品者の場合、購入者を評価
                    : $tradingProduct->seller_id; // 購入者の場合、出品者を評価

        // すでに評価されているか確認
        $existingRating = Rating::where('rater_id', $rater_id)
                                ->where('rated_id', $rated_id)
                                ->where('product_id', $product_id)
                                ->first();

        if ($existingRating) {
            return redirect()->route('chat.show', ['product_id' => $product_id])
                            ->with('error', 'すでに評価済みです。');
        }

        // 新しい評価を作成
        try {
            $rating = new Rating();
            $rating->rater_id = $rater_id;
            $rating->rated_id = $rated_id;
            $rating->score = $request->score;
            $rating->product_id = $product_id;
            $rating->save();  // 保存

            // 取引ステータスを「取引終了」に更新
            $tradingProduct->status = '取引完了';
            $tradingProduct->save();

            // 取引が完了した後に表示を更新
            return redirect()->route('chat.show', ['product_id' => $product_id])
                            ->with('success', '評価が完了しました');
        } catch (\Exception $e) {
            Log::error('評価の保存に失敗: ' . $e->getMessage());
            return redirect()->route('chat.show', ['product_id' => $product_id])
                            ->with('error', '評価の保存に失敗しました');
        }
    }
}
