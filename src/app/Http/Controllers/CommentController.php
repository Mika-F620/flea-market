<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Product;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'content' => 'required|string|max:255',
        ]);

        $comment = Comment::create([
            'product_id' => $validated['product_id'],
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        // 正しい JSON レスポンスを返す
        return response()->json([
            'success' => true,
            'comment' => $comment,
            'user_name' => $comment->user->name, // コメント投稿者の名前を含める
        ], 200); // HTTP 200 ステータスコードを明示
    }
}
