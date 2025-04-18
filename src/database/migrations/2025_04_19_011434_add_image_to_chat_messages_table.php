<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToChatMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('image')->nullable(); // 画像のカラムを追加
        });
    }

    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('image'); // 画像カラムを削除
        });
    }
}
