<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Product;


class ChatController extends Controller
{
    /**
     * チャット画面を表示
     */
    public function show($product_id)
{
    $sender_id = Auth::id(); // 現在ログインしているユーザーID

    // 商品情報を取得
    $product = Product::findOrFail($product_id);

    // 出品者の情報を取得（リレーションを通じて取得）
    $seller = $product->user; // 商品に関連するユーザー（出品者）

    // 出品者のIDをreceiver_idとして設定
    $receiver_id = $product->user_id; // ここが出品者のIDです

    // 送信者と受信者でメッセージを取得
    $messages = ChatMessage::where(function($query) use ($sender_id, $receiver_id) {
        $query->where('sender_id', $sender_id)->where('receiver_id', $receiver_id);
    })
    ->orWhere(function($query) use ($sender_id, $receiver_id) {
        $query->where('sender_id', $receiver_id)->where('receiver_id', $sender_id);
    })
    ->orderBy('created_at', 'asc') // メッセージを時間順にソート（古い順）
    ->get();

    // ビューにメッセージ、出品者情報、商品情報を渡す
    return view('chat.show', compact('messages', 'receiver_id', 'seller', 'product'));
}




    















    /**
     * メッセージを送信
     */
    public function sendMessage(Request $request)
{
    $request->validate([
        'message' => 'required|string|max:500',
        'receiver_id' => 'required|exists:users,id',
        'product_id' => 'required|exists:products,id',
    ]);

    $sender_id = Auth::id();
    $receiver_id = $request->receiver_id;
    $message = $request->message;
    $product_id = $request->product_id;

    // メッセージをデータベースに保存
    ChatMessage::create([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $message,
        'product_id' => $product_id,
    ]);

    return redirect()->route('chat.show', ['product_id' => $product_id]);
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
