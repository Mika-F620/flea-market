<?php

// database/migrations/2025_04_18_021529_add_product_id_to_chat_messages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToChatMessagesTable extends Migration
{
    public function up()
    {
        // `product_id` カラムを追加
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable(); // 必要に応じてnullable()を使う
        });

        // 外部キー制約を追加
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        // 外部キー制約を削除
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        // `product_id` カラムを削除
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
}
