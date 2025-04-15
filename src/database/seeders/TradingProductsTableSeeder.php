<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class TradingProductsTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::factory()->create(); // ダミーユーザーを生成（または既存のユーザーを使用）
        
        // 「取引中の商品」のシーディングデータ
        DB::table('trading_products')->insert([
            [
                'user_id' => $user->id,
                'name' => '腕時計',
                'price' => 15000,
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg'),
                'status' => '取引中', // 「取引中」としてマーク
            ],
            [
                'user_id' => $user->id,
                'name' => 'HDD',
                'price' => 5000,
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg'),
                'status' => '取引中', // 「取引中」としてマーク
            ],
            [
                'user_id' => $user->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg'),
                'status' => '取引中', // 「取引中」としてマーク
            ],
            [
                'user_id' => $user->id,
                'name' => '革靴',
                'price' => 4000,
                'image' => $this->storeImage('https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg'),
                'status' => '取引中', // 「取引中」としてマーク
            ],
            // 他の商品データも追加可能
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
