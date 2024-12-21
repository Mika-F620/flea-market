<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store($productId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'ログインが必要です。'], 401);
        }

        if (!Like::where('user_id', $user->id)->where('product_id', $productId)->exists()) {
            Like::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleLike($productId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'ログインが必要です。'], 401);
        }

        // すでにいいねしているか確認
        $like = Like::where('user_id', $user->id)
                    ->where('product_id', $productId)
                    ->first();

        if ($like) {
            // いいねを解除
            $like->delete();
            $liked = false;
        } else {
            // いいねを追加
            Like::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $liked = true;
        }

        // いいねの総数を取得
        $likeCount = Like::where('product_id', $productId)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likeCount' => $likeCount,
        ]);
    }
}