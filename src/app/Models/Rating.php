<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    // テーブルの定義
    protected $table = 'ratings';

    // マスアサインメントの設定
    protected $fillable = ['rater_id', 'rated_id', 'score'];

    // 評価者とのリレーション
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    // 評価されるユーザーとのリレーション
    public function rated()
    {
        return $this->belongsTo(User::class, 'rated_id');
    }
}
