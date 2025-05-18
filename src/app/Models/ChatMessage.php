<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'product_id',
        'message',
        'image',
        'is_read',
    ];

    // デフォルト値を明示的に設定
    protected $attributes = [
        'is_read' => 0, // デフォルトで未読
    ];

    // saveメソッドをオーバーライドして強制的にis_readの値を保持する
    public function save(array $options = [])
    {
        // 現在のis_readの値を保存
        $isRead = $this->is_read;
        
        // 保存処理を実行
        $result = parent::save($options);
        
        // もし保存後にis_readの値が変わっていたら、強制的に元の値に戻す
        if ($this->is_read !== $isRead) {
            \DB::table('chat_messages')
                ->where('id', $this->id)
                ->update(['is_read' => $isRead]);
                
            // モデルの値も戻す
            $this->is_read = $isRead;
        }
        
        return $result;
    }

    // リレーション定義
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}