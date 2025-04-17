<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id(); // メッセージID
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // 送信者ID（usersテーブルとのリレーション）
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); // 受信者ID（usersテーブルとのリレーション）
            $table->text('message'); // メッセージ内容
            $table->timestamps(); // 作成日時・更新日時
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
