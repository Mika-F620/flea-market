<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTradingProductsTable extends Migration
{
    public function up()
    {
        Schema::table('trading_products', function (Blueprint $table) {
            // 'seller_id' と 'buyer_id' を追加
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // 出品者ID
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade'); // 購入者ID（nullable を削除）
        });
    }

    public function down()
    {
        Schema::table('trading_products', function (Blueprint $table) {
            // 追加したカラムを削除
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['buyer_id']);
            $table->dropColumn(['seller_id', 'buyer_id']);
        });
    }
}