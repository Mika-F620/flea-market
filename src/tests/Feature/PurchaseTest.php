<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    /**
     * 小計画面で変更が即時反映されるテスト
     *
     * @return void
     */
    public function testSubtotalUpdatesImmediatelyWhenPaymentMethodIsSelected()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // ユーザーをログイン
        $this->actingAs($user);

        // 支払い方法選択画面を表示する
        $response = $this->get(route('purchase.show', ['id' => $product->id]));

        // 支払い方法プルダウンメニューが表示されていることを確認
        $response->assertSee('支払い方法');  // 例えば、「支払い方法」のラベルが表示されているか確認
        $response->assertSee('コンビニ払い');
        $response->assertSee('カード払い');

        // 支払い方法を「カード払い」に選択する
        $response = $this->post(route('purchase.store'), [
            'product_id' => $product->id,  // 商品ID
            'payment_method' => 'カード払い',  // 支払い方法
        ]);

        // リダイレクト先のURLを確認
        $response->assertRedirect(route('purchase.show', ['id' => $product->id]));

        // リダイレクト先にアクセスして、支払い方法が反映されていることを確認
        $response = $this->get(route('purchase.show', ['id' => $product->id]));
        
        // 支払い方法が反映されていることを確認
        $response->assertSee('カード払い');  // 正しい支払い方法が反映されていることを確認
    }

    /**
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されているテスト
     *
     * @return void
     */
    public function test_address_is_reflected_in_purchase_screen()
    {
        // 1. ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. 送付先住所を登録
        $response = $this->post(route('purchase.address.update', ['id' => 1]), [
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building_name' => 'サンプルビル',
        ]);

        // 3. 商品購入画面を再度開く
        $product = Product::factory()->create(); // 商品を作成
        $response = $this->get(route('purchase.show', ['id' => $product->id])); // 動的に商品IDを使用

        // 4. 登録した住所が正しく表示されているか確認
        $response->assertSee('東京都渋谷区1-1-1');
        $response->assertSee('サンプルビル');
        $response->assertSee('123-4567');
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録されるテスト
     *
     * @return void
     */
    public function test_shipping_address_is_correctly_assigned_to_order()
    {
        // 1. ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. 送付先住所を登録
        $response = $this->post(route('purchase.address.update', ['id' => 1]), [
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building_name' => 'サンプルビル',
        ]);

        // 3. 商品を購入する
        $product = Product::factory()->create(); // 商品を作成
        $response = $this->post(route('purchase.store'), [
            'product_id' => $product->id,
            'payment_method' => 'カード払い',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building_name' => 'サンプルビル',
        ]);

        // 4. 購入情報がデータベースに保存され、住所が紐づいているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building_name' => 'サンプルビル',
        ]);
    }
}