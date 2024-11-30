<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ユーザー名
            $table->string('email')->unique(); // メールアドレス
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // パスワード
            $table->string('postal_code')->nullable(); // 郵便番号
            $table->string('address')->nullable(); // 住所
            $table->string('building_name')->nullable(); // 建物名
            $table->string('profile_image')->nullable(); // 画像保存用カラム
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
