<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
   // RatingController.php

// RatingController.php

public function store(Request $request)
{
    $request->validate([
        'score' => 'required|integer|min:1|max:5',
        'rated_id' => 'required|exists:users,id', // 評価対象のユーザーID（出品者または購入者）
        'product_id' => 'required|exists:products,id', // 商品ID
    ]);

    // ログインユーザーが評価者
    $rater_id = Auth::id();  // 評価をするユーザー（出品者）
    $rated_id = $request->rated_id;  // 評価されるユーザー（購入者）
    $product_id = $request->product_id;  // 商品ID

    // 出品者が購入者に評価する場合
    $rating = new Rating();
    $rating->rater_id = $rater_id;
    $rating->rated_id = $rated_id;
    $rating->score = $request->score;
    $rating->product_id = $product_id;
    $rating->save();  // 評価を保存

    return redirect()->route('mypage')->with('success', '評価が完了しました');
}


}
