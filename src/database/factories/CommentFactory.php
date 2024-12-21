<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // ユーザーをランダムで生成
            'product_id' => Product::factory(), // 商品をランダムで生成
            'content' => $this->faker->text, // コメント内容
        ];
    }
}