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
        // メッセージの保存
        $message = new ChatMessage();
        $sender_id = Auth::id();  // 送信者（現在ログイン中のユーザー）のID

        // 送信された product_id を取得
        $product_id = $request->input('product_id');

        if (!$product_id) {
            // product_id が空またはNULLの場合、エラーメッセージを表示
            return redirect()->back()->with('error', '商品IDが正しく指定されていません。');
        }

        // 商品IDから取引情報を取得
        $tradingProduct = TradingProduct::where('product_id', $product_id)->first();

        if (!$tradingProduct) {
            // 取引情報が存在しない場合、エラーメッセージを表示
            return redirect()->back()->with('error', '取引情報が見つかりません。');
        }

        // 受信者のIDは、送信者とは異なるユーザー（出品者または購入者）
        $receiver_id = ($sender_id == $tradingProduct->buyer_id) ? $tradingProduct->seller_id : $tradingProduct->buyer_id;

        // メッセージ情報を設定
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiver_id;
        $message->product_id = $product_id;  // 送信された商品IDを保存

        // メッセージが空の場合でも空文字を代入
        $message->message = $request->message ?? '';  // 空メッセージの場合は空文字に設定

        // メッセージが送信されたとき、受信者が未読なので `is_read` は 0 にする
        $message->is_read = 0;

        // 画像の保存（画像が送信されている場合）
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('chat_images', 'public');
            $message->image = $imagePath;
        }

        // メッセージの保存
        $message->save();

        // チャット画面にリダイレクト
        return redirect()->route('chat.show', ['product_id' => $product_id]);
    }

    public function show($product_id)
    {
        $current_user = Auth::user();
        $product = Product::findOrFail($product_id);
        $seller = $product->user;
        $is_seller = ($current_user->id === $product->user_id);

        // 取引情報を取得
        $tradingProduct = TradingProduct::where('product_id', $product_id)->first();

        if (!$tradingProduct) {
            $tradingProduct = TradingProduct::create([
                'product_id' => $product_id,
                'user_id' => $current_user->id,
                'name' => $product->name,
                'image' => $product->image ?? 'default.jpg',
                'status' => '取引中',
            ]);
        }

        // 出品者と購入者を判別
        $buyer_id = ($is_seller) ? $tradingProduct->user_id : $current_user->id;
        $buyer = User::find($buyer_id);

        // メッセージを取得
        $messages = ChatMessage::where('product_id', $product_id)
                                ->orderBy('created_at', 'asc')
                                ->get();

        // メッセージを受信したユーザーが未読ならis_readを1に更新
        foreach ($messages as $message) {
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

        // ログイン中のユーザー以外の取引相手のプロフィール情報を表示するための判定
        if ($is_seller) {
            // 出品者がログイン中 → 購入者の情報を表示
            $profileImage = $buyer->profile_image ? asset('storage/' . $buyer->profile_image) : asset('img/dammy2.png');
            $profileName = $buyer->name;
        } else {
            // 購入者がログイン中 → 出品者の情報を表示
            $profileImage = $seller->profile_image ? asset('storage/' . $seller->profile_image) : asset('img/dammy2.png');
            $profileName = $seller->name;
        }

        return view('chat.show', compact('messages', 'seller', 'product', 'buyer', 'tradingProduct', 'other_products', 'is_seller', 'profileImage', 'profileName'));
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