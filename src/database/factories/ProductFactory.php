<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(100, 10000),
            'image' => 'dummy_image.jpg', // 適切な画像パスを設定
            'user_id' => User::factory(), // ユーザーのファクトリを呼び出し
            'condition' => 'new', // 商品の状態
        ];
    }
}