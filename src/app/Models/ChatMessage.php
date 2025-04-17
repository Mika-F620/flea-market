<?php

// ChatMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['product_id', 'sender_id', 'receiver_id', 'message'];

    // 送信者とのリレーション
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');  // sender_idがUserテーブルのidを参照
    }

    // 受信者とのリレーション
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');  // receiver_idがUserテーブルのidを参照
    }
}
