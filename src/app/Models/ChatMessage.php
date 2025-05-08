<?php

// ChatMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 'chat_messages'; // テーブル名の確認
    
    // リレーションの定義
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // リレーション設定
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
