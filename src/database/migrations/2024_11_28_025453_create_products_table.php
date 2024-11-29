<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // 商品ID
            $table->unsignedBigInteger('user_id'); // 出品者のユーザーID
            $table->string('image'); // 商品画像のパス
            $table->json('categories')->nullable(); // カテゴリを追加
            $table->string('condition'); // 商品の状態
            $table->string('name'); // 商品名
            $table->text('description'); // 商品の説明
            $table->integer('price'); // 販売価格
            $table->timestamps();

            // 外部キー制約（ユーザーIDと紐付け）
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
