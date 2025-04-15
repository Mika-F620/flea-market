<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('trading_products')->onDelete('cascade'); // 取引中の商品に関連
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // メッセージ送信者
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); // メッセージ受信者
            $table->text('message'); // メッセージ
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
