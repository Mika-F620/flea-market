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
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * 取引完了後の評価画面を表示
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function completeTransaction($productId)
    {
        // 商品情報を取得
        $product = Product::findOrFail($productId);
        $seller = $product->user; // 出品者の情報

        // 取引完了時、取引中のステータスを「取引完了」に変更
        $tradingProduct = TradingProduct::where('product_id', $productId)
                                        ->where('user_id', $seller->id) // 出品者の取引情報を更新
                                        ->first();

        if ($tradingProduct) {
            $tradingProduct->status = '取引完了';  // 取引完了に変更
            $tradingProduct->save();
        }

        // 取引完了メールを出品者に送信
        try {
            Mail::to($seller->email)->send(new TransactionCompleted($product, $seller));
            Log::info('取引完了通知メールが送信されました。');
        } catch (\Exception $e) {
            Log::error('メール送信エラー: ' . $e->getMessage());
            return redirect()->route('chat.show', ['product_id' => $productId])
                            ->with('error', '取引完了メールの送信に失敗しました');
        }

        // 取引完了画面を表示
        return redirect()->route('chat.show', ['product_id' => $productId])->with('success', '取引が完了しました。');
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
        $product = Product::findOrFail($productId);

        // ログイン中のユーザー
        $currentUser = Auth::user();
        
        // 出品者を取得
        $seller = $product->user;
        
        // 評価情報を保存
        $rating = new Rating();
        $rating->rater_id = $currentUser->id; // ログイン中のユーザー（評価する側）
        $rating->rated_id = $request->rated_id; // 評価される側
        $rating->product_id = $productId; // 商品ID
        $rating->score = $request->score; // 評価スコア
        $rating->comment = $request->comment ?? ''; // コメント（省略可）
        $rating->save();  // 保存
        
        // 購入者が出品者を評価した場合のみメールを送信
        if ($currentUser->id != $seller->id && $request->rated_id == $seller->id) {
            // デバッグ用ログを追加
            Log::debug('評価のデータ確認:', [
                'current_user_id' => $currentUser->id,
                'seller_id' => $seller->id,
                'rated_id' => $request->rated_id,
                'seller_email' => $seller->email,
                'product_name' => $product->name,
                'rating_score' => $rating->score
            ]);
            
            // メール送信処理
            try {
                // メールの送信先とデータを明示的にログに記録
                Log::info('評価完了メール送信準備:', [
                    'seller_email' => $seller->email,
                    'product_name' => $product->name,
                    'rating_score' => $rating->score
                ]);
                
                // 出品者にメール送信 - メールを直接作成して内容確認
                $email = new RatingCompleted($seller, $product, $rating);
                Mail::to($seller->email)->send($email);
                
                // 送信成功をログに記録
                Log::info('評価完了メールが送信されました。送信先: ' . $seller->email);
            } catch (\Exception $e) {
                // エラー詳細をログに記録
                Log::error('評価完了メール送信エラー: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                    'seller_email' => $seller->email
                ]);
                
                return redirect()->route('chat.show', ['product_id' => $productId])
                                ->with('error', '評価完了メールの送信に失敗しました: ' . $e->getMessage());
            }
        } else {
            // 条件に合わなかった場合の理由をログに記録
            Log::info('メール送信条件不成立:', [
                'current_user_id' => $currentUser->id,
                'seller_id' => $seller->id,
                'rated_id' => $request->rated_id
            ]);
        }

        // 取引情報を取得
        $tradingProduct = TradingProduct::where('product_id', $productId)
                                      ->where(function ($query) use ($currentUser, $seller) {
                                          $query->where('buyer_id', $currentUser->id)
                                              ->orWhere('seller_id', $seller->id);
                                      })
                                      ->first();

        if ($tradingProduct) {
            // 取引ステータスを「取引完了」に更新する前に、評価が双方完了しているかを確認
            $sellerRated = Rating::where('rater_id', $seller->id)
                                 ->where('product_id', $productId)
                                 ->exists();

            $buyerRated = Rating::where('rater_id', '!=', $seller->id)
                                ->where('product_id', $productId)
                                ->exists();

            // 両者が評価した場合にのみ取引ステータスを変更
            if ($sellerRated && $buyerRated) {
                $tradingProduct->status = '取引完了'; // 取引完了に変更
                $tradingProduct->save();
                
                Log::info('両者の評価が完了しました。取引ステータスを「取引完了」に変更しました。');
            }
        }

        return redirect()->route('chat.show', ['product_id' => $productId])
                        ->with('success', '評価が完了しました');
    }

    public function showMypage(Request $request)
    {
        // ログイン中のユーザーを取得
        $user = Auth::user();
        $page = $request->get('page', 'sell'); // 現在のページ（出品・購入・取引中）

        // 取引中の商品を取得（購入者として取引中の商品 + 出品者として取引中の商品）
        if ($page === 'trading') {
            $products = TradingProduct::where(function ($query) use ($user) {
                // 購入者としての取引中の商品
                $query->where('buyer_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                // 出品者としての取引中の商品
                $query->where('seller_id', $user->id);
            })
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

        // ユーザーの評価を計算（購入者として、または出品者としての評価平均）
        $averageRating = $user->averageRating();

        return view('mypage', compact('user', 'products', 'page', 'averageRating'));
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
                // 商品が購入されたかどうかを判定
                $product->is_sold = Purchase::where('product_id', $product->id)->exists();
            }
        } elseif ($page === 'buy') {
            // 購入済み商品を取得
            $products = Purchase::where('user_id', $user->id)
                ->with('product') // 購入商品情報を取得
                ->get()
                ->pluck('product'); // 購入商品データのみ抽出
        } elseif ($page === 'trading') {
            // 取引中の商品を取得（出品者としての取引中商品）
            $products = TradingProduct::where('user_id', $user->id)
                ->where('status', '取引中') // 取引中の商品
                ->get();
        } else {
            $products = collect(); // 空のコレクション
        }

        return view('mypage', compact('user', 'page', 'products'));
    }

    public function startTransaction(Request $request, $productId)
    {
        // 商品情報を取得
        $product = Product::findOrFail($productId);

        // ログインしているユーザー（購入者）を取得
        $buyer = Auth::user();

        // 出品者の情報
        $seller = $product->user;

        // 既に取引中のレコードが存在しないか確認
        $existingTransaction = TradingProduct::where('product_id', $productId)
            ->where(function($query) use ($buyer, $seller) {
                $query->where('user_id', $buyer->id)
                    ->orWhere('user_id', $seller->id);
            })
            ->first();

        if ($existingTransaction) {
            // 既に取引中の商品があれば、そのレコードを再利用
            return redirect()->route('chat.show', ['product_id' => $productId])
                            ->with('success', '既に取引が開始されています');
        }

        // 取引中の商品を作成（出品者と購入者両方のユーザーIDを同じレコードに保存）
        TradingProduct::create([
            'product_id' => $productId,  // 商品ID
            'user_id' => $buyer->id,     // 購入者ID
            'name' => $product->name,    // 商品名
            'image' => $product->image,  // 商品画像
            'price' => $product->price,  // 価格
            'status' => '取引中',        // 取引中の状態
        ]);

        // 出品者にも取引を表示させるため、同じ商品で取引中として1つのレコードを作成
        TradingProduct::create([
            'product_id' => $productId,  // 商品ID
            'user_id' => $seller->id,    // 出品者ID
            'name' => $product->name,    // 商品名
            'image' => $product->image,  // 商品画像
            'price' => $product->price,  // 価格
            'status' => '取引中',        // 取引中の状態
        ]);

        // チャット画面に遷移
        return redirect()->route('chat.show', ['product_id' => $productId])
                        ->with('success', '取引が開始されました');
    }

    public function sendRatingEmail(Request $request)
    {
        try {
            // フォームから送信されたデータを取得
            $product = Product::findOrFail($request->input('product_id'));
            $seller = User::findOrFail($request->input('seller_id'));
            $ratingScore = $request->input('score'); // 評価スコア

            // 1. 評価のデータベース保存
            $rating = new Rating();
            $rating->rater_id = Auth::id();  // 評価者
            $rating->rated_id = $seller->id; // 出品者（評価対象）
            $rating->product_id = $product->id; // 商品ID
            $rating->score = $ratingScore; // 評価スコアを保存
            $rating->save();  // 保存

            // 購入者が評価をした場合のみメール送信
            if (Auth::id() !== $seller->id) {
                // 2. メール送信処理
                Mail::to($seller->email)->send(new RatingCompleted($seller, $product, $rating));
            }

            // 3. 成功時のレスポンス
            return response()->json(['success' => true, 'message' => 'メールが送信されました']);
        } catch (\Exception $e) {
            // エラーハンドリング：エラー内容をログに記録
            Log::error('メール送信エラー: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}