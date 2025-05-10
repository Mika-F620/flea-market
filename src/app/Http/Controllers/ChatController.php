<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\TradingProduct;
use App\Http\Requests\MessageRequest;

class ChatController extends Controller
{
    public function start(Request $request)
    {
        // 受け取った情報を変数にセット
        $productId = $request->input('product_id');
        $sellerId = $request->input('seller_id');
    
        // 商品情報を取得
        $product = Product::findOrFail($productId);
        $seller = $product->user;
        $buyer = Auth::user(); // 現在ログインしているユーザー（購入者）
    
        // チャットの開始（必要に応じてTradingProductモデルを使って取引状態にする）
        TradingProduct::updateOrCreate([
            'product_id' => $productId,
            'user_id' => $buyer->id,  // 現在ログイン中のユーザー（購入者）
        ], [
            'status' => '取引中',  // 取引中の状態にする
        ]);
    
        // チャット画面にリダイレクト
        return redirect()->route('chat.show', $productId);
    }

    public function sendMessage(MessageRequest $request)
    {
        session(['chatMessage' => $request->input('message')]);

        // バリデーションが成功した場合
        // メッセージの保存
        $message = new ChatMessage();
        $message->sender_id = Auth::id();
        $message->receiver_id = $request->receiver_id;
        $message->product_id = $request->product_id; // ここで product_id をセット

        // メッセージが空の場合でも空文字を代入
        $message->message = $request->message ?? '';  // 空メッセージの場合は空文字に設定

        // 画像の保存
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('chat_images', 'public');
            $message->image = $imagePath;
        }

        // メッセージの保存
        $message->save();

        session()->forget('chatMessage');

        // チャット画面にリダイレクト
        return redirect()->route('chat.show', ['product_id' => $request->product_id]);
    }

   public function show($product_id)
    {
        $current_user = Auth::user(); // 現在ログインしているユーザー
        $product = Product::findOrFail($product_id); // 商品情報を取得
        $seller = $product->user; // 出品者情報
        $is_seller = ($current_user->id === $product->user_id); // ログインユーザーが出品者かどうか

        // 取引情報を取得
        $tradingProduct = TradingProduct::where('product_id', $product_id)->first();

        // 取引情報がない場合（まだ決済後の処理が完了していない場合）
        if (!$tradingProduct) {
            // 決済直後の場合は、取引情報を新規作成
            $tradingProduct = TradingProduct::create([
                'product_id' => $product_id,
                'user_id' => $current_user->id,
                'seller_id' => $product->user_id, // 出品者IDを明示的に設定
                'buyer_id' => $is_seller ? null : $current_user->id, // 購入者IDを設定
                'name' => $product->name,
                'image' => $product->image ?? 'default.jpg',
                'status' => '取引中',
            ]);
        }

        // 購入者を特定
        $buyer_id = $is_seller ? $tradingProduct->user_id : $current_user->id;
        $buyer = User::find($buyer_id);

        // メッセージを取得 - sender との関連付けを eager loading
        $messages = ChatMessage::with('sender')
                            ->where('product_id', $product_id)
                            ->orderBy('created_at', 'asc')
                            ->get();

        // メッセージの表示を受信者のみに更新
        foreach ($messages as $message) {
            // 受信者のみに 'is_read' を更新
            if ($message->receiver_id == $current_user->id && $message->is_read == 0) {
                $message->is_read = 1;  // 既読にする
                $message->save();
            }
        }

        // 他の取引中の商品を取得（サイドバー用）
        $other_products = TradingProduct::where(function ($query) use ($current_user) {
            $query->where('seller_id', $current_user->id)
                ->orWhere('buyer_id', $current_user->id);
        })
        ->where('status', '取引中')
        ->with('product')
        ->get();

        return view('chat.show', compact('messages', 'seller', 'product', 'buyer', 'tradingProduct', 'other_products', 'is_seller'));
    }

    // 編集ページ表示
    public function editMessagePage($message_id)
    {
        $message = ChatMessage::findOrFail($message_id);
        
        // 編集ページにメッセージを渡して表示
        return view('chat.edit', compact('message'));
    }

    // メッセージ更新
    public function editMessage(Request $request, $message_id)
    {
        $message = ChatMessage::findOrFail($message_id);

        // 編集するメッセージのバリデーション
        $request->validate(['message' => 'required|string|max:500']);

        // メッセージを更新
        $message->message = $request->message;
        $message->save();

        // 更新後、元のチャットページにリダイレクト
        return redirect()->route('chat.show', ['product_id' => $message->product_id])->with('success', 'メッセージが更新されました');
    }

    /**
     * メッセージを削除
     */
    public function deleteMessage($message_id)
    {
        $message = ChatMessage::findOrFail($message_id);

        // メッセージの送信者が現在のユーザーでない場合はエラー
        if ($message->sender_id !== Auth::id()) {
            return redirect()->route('chat.show', ['product_id' => $message->product_id])->with('error', '他のユーザーのメッセージは削除できません');
        }

        // メッセージを削除
        $message->delete();

        return redirect()->route('chat.show', ['product_id' => $message->product_id])->with('success', 'メッセージが削除されました');
    }
}