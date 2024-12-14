<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfilePageTest extends TestCase
{
    /**
     * プロフィールページの情報が正しく表示されることのテスト
     *
     * @return void
     */
    // public function test_profile_page_displays_correctly()
    // {
    //     // 1. ユーザーを作成してログイン
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // 2. 出品した商品を作成
    //     $product1 = Product::factory()->create([
    //         'user_id' => $user->id,
    //     ]);
    //     $product2 = Product::factory()->create([
    //         'user_id' => $user->id,
    //     ]);

    //     // 3. 購入した商品を作成
    //     $product3 = Product::factory()->create();
    //     $this->post(route('purchase.store'), [
    //         'product_id' => $product3->id,
    //         'payment_method' => 'カード払い',
    //         'postal_code' => '123-4567',
    //         'address' => '東京都渋谷区1-1-1',
    //         'building_name' => 'サンプルビル',
    //     ]);

    //     // 4. プロフィールページにアクセス
    //     $response = $this->get(route('profile.edit'));

    //     // 5. 必要な情報が表示されているか確認
    //     $response->assertStatus(200);
    //     $response->assertSee($user->name); // ユーザー名
    //     $response->assertSee($user->profile_image); // プロフィール画像（もし設定されていれば）
    //     $response->assertSee($product1->name); // 出品した商品1
    //     $response->assertSee($product2->name); // 出品した商品2
    //     $response->assertSee($product3->name); // 購入した商品
    // }

    /**
     * プロフィールページに表示される初期値が正しいか確認するテスト
     *
     * @return void
     */
    public function test_profile_page_displays_initial_values_correctly()
    {
        // 1. ユーザーを作成してログイン
        $user = User::factory()->create([
            'name' => 'Test User',
            'profile_image' => 'path/to/profile_image.jpg', // プロフィール画像の初期設定
            'postal_code' => '123-4567', // 初期設定の郵便番号
            'address' => '東京都渋谷区1-1-1', // 初期設定の住所
            'building_name' => 'サンプルビル', // 初期設定の建物名
        ]);
        $this->actingAs($user);

        // 2. プロフィールページにアクセス
        $response = $this->get(route('profile.edit'));

        // 3. 必要な情報が正しく表示されているか確認
        $response->assertStatus(200);
        $response->assertSee($user->name); // ユーザー名
        $response->assertSee($user->profile_image); // プロフィール画像
        $response->assertSee($user->postal_code); // 郵便番号
        $response->assertSee($user->address); // 住所
        $response->assertSee($user->building_name); // 建物名
    }
}
