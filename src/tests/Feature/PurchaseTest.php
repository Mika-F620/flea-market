<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    // use RefreshDatabase;

    /**
     * 商品購入が完了するテスト
     *
     * @return void
     */
//     public function test_user_can_complete_purchase()
// {
//     // 1. ユーザーを作成してログインする
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 2. 商品を作成する
//     $product = Product::factory()->create([
//         'name' => 'Test Product',
//         'price' => 1000,
//         'description' => 'Test product description',
//         'image' => 'dummy_image.jpg',
//         'user_id' => $user->id, // ユーザーIDを設定
//         'condition' => 'new',
//     ]);

//     // 3. 商品購入画面にアクセス
//     $response = $this->get(route('purchase.show', $product->id));

//     // 4. 商品購入ボタンを押下
//     $response = $this->post(route('purchase.store', $product->id), [
//         'payment_method' => 'コンビニ払い', // 支払い方法の選択
//         'postal_code' => '123-4567',
//         'address' => '東京都渋谷区1-1-1',
//         'building_name' => 'サンプルビル',
//     ]);

//     // 5. 購入完了後のリダイレクト先の確認
//     $response->assertRedirect(route('purchase.show', ['id' => $product->id])); // 修正されたリダイレクト先
//     $response->assertSessionHas('success', '購入が完了しました。'); // セッションメッセージの確認

//     // 6. 購入情報がデータベースに保存されているか確認
//     $this->assertDatabaseHas('orders', [
//         'user_id' => $user->id,
//         'product_id' => $product->id,
//         'status' => 'completed',
//     ]);

//     // 7. 商品の在庫が減っているか確認
//     $product->refresh();
//     $this->assertEquals(9, $product->stock); // 在庫が1減っていることを確認
// }

/**
     * 購入した商品が商品一覧画面にて「sold」と表示されることのテスト
     *
     * @return void
     */
//     public function test_purchased_product_is_marked_as_sold()
// {
//     // 1. ユーザーを作成してログインする
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 2. 商品を作成する（stockカラムを削除）
//     $product = Product::factory()->create([
//         'name' => 'Test Product',
//         'price' => 1000,
//         'condition' => 'new',  // stock削除後
//     ]);

//     // 3. 商品購入画面にアクセス
//     $response = $this->get(route('purchase.show', $product->id));

//     // 4. 商品購入ボタンを押下
//     $response = $this->post(route('purchase.store'), [
//         'product_id' => $product->id,
//         'payment_method' => 'コンビニ払い',  // 例として
//         'postal_code' => '123-4567',
//         'address' => 'Tokyo, Japan',
//     ]);

//     // 5. 商品一覧画面に遷移
//     $response = $this->get(route('products.index'));

//     // 6. 購入した商品が「sold」と表示されているか確認
//     $response->assertSee('sold');
// }

// public function test_user_can_see_purchased_products_in_profile()
// {
//     // 1. ユーザーにログインする
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 2. 商品購入画面を開く
//     $product = Product::factory()->create([
//         'name' => 'Test Product',
//         'price' => 1000,
//     ]);

//     // 3. 商品を選択して「購入する」ボタンを押下
//     $this->post(route('purchase.store'), [
//         'product_id' => $product->id,
//         'payment_method' => 'コンビニ払い',
//         'postal_code' => '123-4567',
//         'address' => 'Tokyo, Japan',
//     ]);

//     // 4. プロフィール画面を表示する
//     $response = $this->get(route('profile.show'));

//     // 5. 購入した商品がプロフィール画面に表示されているか確認
//     $response->assertSee($product->name);
//     $response->assertSee(number_format($product->price));
// }


// public function test_payment_method_is_immediately_reflected()
// {
//     // 1. ユーザーを作成してログイン
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 2. 商品を作成
//     $product = Product::factory()->create([
//         'name' => 'Test Product',
//         'price' => 1000,
//     ]);

//     // 3. 支払い方法選択画面にアクセス
//     $response = $this->get(route('purchase.show', $product->id));

//     // 4. 支払い方法を選択
//     $response = $this->post(route('purchase.store'), [
//         'product_id' => $product->id,
//         'payment_method' => 'カード払い', // 選択する支払い方法
//     ]);

//     // 5. 支払い方法が正しく選択されたことを確認
//     $response->assertRedirect(route('purchase.show', ['id' => $product->id]));  // 修正されたリダイレクト先
//     $response->assertSessionHas('payment_method', 'カード払い');  // セッションに支払い方法が反映されていることを確認
// }

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
