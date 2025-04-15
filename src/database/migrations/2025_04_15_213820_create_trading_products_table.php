<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradingProductsTable extends Migration
{
    public function up()
    {
        Schema::create('trading_products', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->string('name'); // 商品名
            $table->string('image'); // 商品画像
            $table->decimal('price', 10, 2); // 料金 (例: 9999.99)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 出品者のID (usersテーブルとのリレーション)
            $table->enum('status', ['取引中', '取引完了'])->default('取引中'); // 取引の状態
            $table->timestamps(); // 作成日時・更新日時
        });
    }

    public function down()
    {
        Schema::dropIfExists('trading_products');
    }
}
