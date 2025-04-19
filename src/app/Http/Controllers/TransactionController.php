<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Mail\TransactionCompleted;
use App\Mail\RatingCompleted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\TradingProduct;
use App\Models\Purchase;



class TransactionController extends Controller
{
    /**
     * 取引完了後の評価画面を表示
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function completeTransaction($productId)
    {
        // 商品の情報を取得
        $product = Product::findOrFail($productId);
        $seller = $product->user; // 出品者の情報

        // 取引完了画面を表示
        return view('transaction.complete', compact('product', 'seller'));
    }

    /**
     * 評価完了後に出品者に評価結果を送信
     * @param  Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function storeRating(Request $request, $productId)
{
    // 商品が存在するか確認
    $product = Product::find($productId);
    if (!$product) {
        return redirect()->route('products.index')->with('error', '指定された商品が存在しません。');
    }

    // 評価情報を保存
    $rating = new Rating();
    $rating->rater_id = auth()->id(); // ログイン中のユーザー
    $rating->rated_id = $request->rated_id; // 出品者
    $rating->product_id = $productId; // 商品ID
    $rating->score = $request->score; // 評価スコア
    $rating->comment = $request->comment ?? ''; // コメント（省略可）
    $rating->save();

    // 出品者に評価完了メールを送信
    $seller = $product->user; // 出品者

    // メール送信処理
    try {
        Mail::to($seller->email)->send(new RatingCompleted($seller, $product, $rating));
    } catch (\Exception $e) {
        // メール送信失敗時の処理
        return redirect()->route('products.index')->with('error', 'メール送信に失敗しました。');
    }

    // メール送信後、商品一覧画面にリダイレクト
    return redirect()->route('products.index')->with('message', '取引が完了しました。評価が送信されました。');
}

public function showMypage(Request $request)
{
    // ログイン中のユーザーを取得
    $user = auth()->user();
    $page = $request->get('page', 'sell'); // 現在のページ（出品・購入・取引中）

    if ($page === 'trading') {
        // 取引中の商品を取得
        $products = TradingProduct::where('user_id', $user->id)
            ->where('status', '取引中') // 取引中の商品
            ->get();
    } elseif ($page === 'buy') {
        // 購入した商品を取得
        $products = Purchase::where('user_id', $user->id)
            ->with('product')
            ->get()
            ->pluck('product'); // 購入商品データのみ抽出
    } else {
        // 出品した商品を取得
        $products = Product::where('user_id', $user->id)->get(); // 出品中の商品
    }

    return view('mypage', compact('user', 'products', 'page'));
}




public function mypage(Request $request)
{
    // ログイン状態を確認
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'ログインが必要です。');
    }

    // ログイン中のユーザーを取得
    $user = Auth::user();
    $page = $request->query('page', 'sell'); // デフォルトで 'sell'

    // 出品商品か購入商品を取得
    if ($page === 'sell') {
        $products = Product::where('user_id', $user->id)->get();
        // 出品した商品が購入されたかどうか確認
        foreach ($products as $product) {
            $product->is_sold = Purchase::where('product_id', $product->id)->exists();
        }
    } elseif ($page === 'buy') {
        // 購入済み商品を取得
        $products = Purchase::where('user_id', $user->id)
            ->with('product') // 購入商品情報を取得
            ->get()
            ->pluck('product'); // 購入商品データのみ抽出
    } else {
        $products = collect(); // 空のコレクション
    }

    return view('mypage', compact('user', 'page', 'products'));
}




}
