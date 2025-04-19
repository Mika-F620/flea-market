<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\TradingProduct;



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

     public function sendMessage(Request $request)
    {
        // バリデーション
        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        // メッセージの保存
        $message = new ChatMessage();
        $message->sender_id = Auth::id();
        $message->receiver_id = $request->receiver_id;
        $message->product_id = $request->product_id;

        // メッセージが空の場合でも空文字を代入
        $message->message = $request->message ?? '';  // 空メッセージの場合は空文字に設定

        // 画像の保存
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('chat_images', 'public');
            $message->image = $imagePath;
        }

        // 購入者と出品者両方に1回だけ保存
        $message->save();

        return redirect()->route('chat.show', ['product_id' => $request->product_id]);
    }

    public function show($product_id)
    {
        $sender_id = Auth::id(); // 現在ログインしているユーザーID
        $product = Product::findOrFail($product_id); // 商品情報を取得
        $seller = $product->user; // 出品者情報
        $receiver_id = $product->user_id; // 出品者ID

        // 購入者情報を取得（取引を開始したユーザー）
        $buyer = TradingProduct::where('product_id', $product_id)
                    ->where('user_id', Auth::id()) // ログインしているユーザーが購入者か確認
                    ->first();

        // もし$buyerがnullであれば、取引を開始したユーザーとして購入者を設定
        if (!$buyer) {
            $buyer = Auth::user();  // 購入者はログインしているユーザー
        }

        // 購入者情報（Userモデル）を取得
        $buyerUser = $buyer->user; // これで購入者のUserモデルが取得できます

        // メッセージを取得
        $messages = ChatMessage::where(function($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $sender_id)->where('receiver_id', $receiver_id);
        })
        ->orWhere(function($query) use ($sender_id, $receiver_id) {
            $query->where('sender_id', $receiver_id)->where('receiver_id', $sender_id);
        })
        ->where('product_id', $product_id) // 商品IDでフィルタ
        ->orderBy('created_at', 'asc') // メッセージを時間順に
        ->get();

        return view('chat.show', compact('messages', 'receiver_id', 'seller', 'product', 'buyer', 'buyerUser'));
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