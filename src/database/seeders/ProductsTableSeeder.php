<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Storage をインポート
use App\Models\User;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $user = User::factory()->create(); // ダミーユーザーを生成する場合、または既存のユーザーを取得する
        // 認証済みのユーザーをシーディング
        $user1 = User::create([
            'name' => '出品者ユーザー1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),  // ユーザーを認証済みとして作成
        ]);

        $user2 = User::create([
            'name' => '出品者ユーザー2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        
        // 商品データをシーディング
        DB::table('products')->insert([
            [
                'user_id' => $user1->id,
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg'),
                'condition' => '良好',
                'categories' => json_encode(['メンズ']),
            ],
            [
                'user_id' => $user1->id,
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg'),
                'condition' => '目立った傷や汚れなし',
                'categories' => json_encode(['家電']),
            ],
            [
                'user_id' => $user1->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg'),
                'condition' => 'やや傷や汚れあり',
                'categories' => json_encode(['キッチン']),
            ],
            [
                'user_id' => $user1->id,
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg'),
                'condition' => '状態が悪い',
                'categories' => json_encode(['メンズ']),
            ],
            [
                'user_id' => $user1->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg'),
                'condition' => '良好',
                'categories' => json_encode(['家電']),
            ],
            [
                'user_id' => $user2->id,
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg'),
                'condition' => '目立った傷や汚れなし',
                'categories' => json_encode(['家電']),
            ],
            [
                'user_id' => $user2->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg'),
                'condition' => 'やや傷や汚れあり',
                'categories' => json_encode(['レディース']),
            ],
            [
                'user_id' => $user2->id,
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg'),
                'condition' => '状態が悪い',
                'categories' => json_encode(['キッチン']),
            ],
            [
                'user_id' => $user2->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg'),
                'condition' => '良好',
                'categories' => json_encode(['キッチン']),
            ],
            [
                'user_id' => $user2->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg'),
                'condition' => '目立った傷や汚れなし',
                'categories' => json_encode(['コスメ']),
            ],
        ]);
    }

    /**
     * 画像URLをストレージに保存
     *
     * @param string $url
     * @return string 保存された画像のパス
     */
    private function storeImage($url)
    {
        // 画像URLを取得
        $imageContent = file_get_contents($url);

        // 保存するユニークな画像名を生成
        $imagePath = 'product_images/' . uniqid() . '.jpg';

        // 画像を public ストレージに保存
        Storage::disk('public')->put($imagePath, $imageContent);

        return $imagePath;
    }
}
