<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
{
    // 評価データを保存
    $rating = new Rating();
    $rating->rater_id = Auth::id(); // 現在のユーザーID
    $rating->rated_id = $request->rated_id;
    $rating->score = $request->score;
    $rating->save();

    // 評価保存後に商品一覧ページにリダイレクト
    return redirect()->route('products.index');
}
}
