<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('ratings', function (Blueprint $table) {
        $table->unsignedBigInteger('product_id'); // product_idカラムを追加
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); // 外部キー制約
    });
}

public function down()
{
    Schema::table('ratings', function (Blueprint $table) {
        $table->dropForeign(['product_id']);
        $table->dropColumn('product_id');
    });
}

}
