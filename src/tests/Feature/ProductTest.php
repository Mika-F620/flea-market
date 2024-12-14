<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductTest extends TestCase
{
  use DatabaseTransactions;

  /**
   * おすすめページで全商品が表示されることをテスト
   *
   * @return void
   */
  public function testAllProductsAreDisplayedOnRecommendedPage()
  {
    // テスト用ユーザーを作成
    $user = \App\Models\User::factory()->create();

    // 商品を作成（商品名と商品画像を含む）
    $product = \App\Models\Product::create([
      'name' => '商品名', 
      'description' => '商品説明', // 商品説明は表示されません
      'price' => 1000,
      'user_id' => $user->id, 
      'image' => 'dummy_image.jpg', // ダミー画像
      'condition' => '新品', 
    ]);

    // 商品一覧ページ（おそらくトップページ）にアクセス
    $response = $this->get('/'); 

    // 商品が表示されていることを確認
    $response->assertSee($product->name);  // 商品名が表示される
    $response->assertSee($product->image);  // 商品画像が表示される（画像のURLが含まれていることを確認）
  }

    // ProductTest.phpの中で、購入済みの商品を作成するテストの例

//     public function testSoldLabelIsDisplayedForPurchasedProduct()
// {
//     // ユーザーを作成
//     $user = User::factory()->create();

//     // 商品を作成
//     $product = Product::factory()->create();

//     // 購入データを作成
//     Purchase::create([
//         'user_id' => $user->id,
//         'product_id' => $product->id,
//         'payment_method' => 'カード払い', // 日本語でカード払いを設定
//         'postal_code' => '123-4567',
//         'address' => '東京都渋谷区1-1-1',
//         'building_name' => '渋谷ビル',
//     ]);

//     // 商品ページにアクセス
//     $response = $this->get(route('products.show', $product->id));

//     // 購入済み商品の場合、「Sold」のラベルが表示されることを確認
//     $response->assertSee('Sold');
// }

  public function testProductDoesNotShowUpInRecommendedForUserWhoListedIt()
  {
    // 1. ユーザーを作成してログイン
    $user = User::factory()->create();
    $this->actingAs($user);

    // 2. ユーザーが出品した商品を作成
    $product = Product::factory()->create([
      'user_id' => $user->id, // 出品者としてユーザーを指定
    ]);

    // 3. 他のユーザーに表示されないかを確認
    $response = $this->get(route('products.index', ['page' => 'recommend']));

    // 4. 自分が出品した商品は「おすすめ」一覧に表示されないことを確認
    $response->assertDontSee($product->name); // 商品名が表示されていないことを確認
  }

  public function testLikedProductsAreShownInMyList()
  {
    // 1. ユーザーを作成してログイン
    $user = User::factory()->create();
    $this->actingAs($user);

    // 2. 商品を作成
    $product = Product::factory()->create();

    // 3. ユーザーが商品にいいねをする
    $user->likes()->create(['product_id' => $product->id]);

    // 4. マイリストページを開く
    $response = $this->get(route('products.index', ['page' => 'mylist']));

    // 5. いいねした商品がマイリストに表示されることを確認
    $response->assertSee($product->name); // 商品名が表示されることを確認
  }

  // public function testPurchasedProductsShowSoldLabel()
  // {
  //   // 1. ユーザーを作成してログイン
  //   $user = User::factory()->create();
  //   $this->actingAs($user);

  //   // 2. 商品を作成
  //   $product = Product::factory()->create();

  //   // 3. ユーザーが商品を購入
  //   $purchase = Purchase::create([
  //       'user_id' => $user->id,
  //       'product_id' => $product->id,
  //       'payment_method' => 'カード払い', // 支払い方法
  //       'postal_code' => '123-4567',
  //       'address' => '東京都千代田区',
  //       'building_name' => 'ビル名',
  //   ]);

  //   // 4. マイリストページを開く
  //   $response = $this->get(route('products.index', ['page' => 'mylist']));

  //   // 5. 購入済み商品に「Sold」のラベルが表示されていることを確認
  //   $response->assertSee('Sold');
  //   $response->assertSee($product->name); // 商品名も表示されることを確認
  // }

//   public function testUserDoesNotSeeTheirOwnProductInMyList()
// {
//     // 1. ユーザーを作成してログイン
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 2. ユーザーが出品した商品を作成
//     $product = Product::factory()->create([
//         'user_id' => $user->id, // このユーザーが出品した商品
//     ]);

//     // 3. 他のユーザーが出品した商品を作成（確認のため）
//     $otherUser = User::factory()->create();
//     $otherProduct = Product::factory()->create([
//         'user_id' => $otherUser->id, // 他のユーザーが出品した商品
//     ]);

//     // 4. マイリストページを開く
//     $response = $this->get(route('products.index', ['page' => 'mylist']));

//     // 5. 自分が出品した商品が表示されていないことを確認
//     $response->assertDontSee($product->name); // 自分が出品した商品名は表示されない

//     // 6. 他のユーザーが出品した商品が表示されていることを確認
//     $response->assertSee($otherProduct->name); // 他のユーザーが出品した商品名は表示される
// }

  public function testNothingIsDisplayedForUnauthenticatedUser()
  {
      // 未認証状態でマイリストページにアクセス
      $response = $this->get(route('products.index', ['page' => 'mylist']));

      // ログインしていない場合、ログインページにリダイレクトされることを確認
      $response->assertRedirect(route('login')); // ログインページにリダイレクトされることを確認

      // リダイレクト先のURLを確認する
      $response->assertStatus(302); // 302リダイレクトステータスコードを確認
  }

  public function testPartialSearchByProductName()
  {
    // ユーザーを作成してログイン
    $user = User::factory()->create();
    $this->actingAs($user);

    // 商品をいくつか作成
    $product1 = Product::factory()->create(['name' => '商品名A']);
    $product2 = Product::factory()->create(['name' => '商品名B']);
    $product3 = Product::factory()->create(['name' => '別の商品']);

    // 検索キーワードを「商品名A」として検索
    $response = $this->get(route('products.index', ['search' => '商品名A']));

    // 「商品名A」を含む商品が表示されていることを確認
    $response->assertSee($product1->name);
    $response->assertDontSee($product2->name); // 部分一致しない商品が表示されないことを確認
    $response->assertDontSee($product3->name);
  }

  public function testSearchQueryIsPersistedOnMyListPage()
  {
    // ユーザーを作成してログイン
    $user = User::factory()->create();
    $this->actingAs($user);

    // 商品をいくつか作成
    $product1 = Product::factory()->create(['name' => '商品名A']);
    $product2 = Product::factory()->create(['name' => '商品名B']);
    $product3 = Product::factory()->create(['name' => '別の商品']);

    // ホームページで検索を実行
    $response = $this->get(route('products.index', ['search' => '商品名A']));

    // 検索結果が表示されることを確認
    $response->assertSee($product1->name);
    $response->assertDontSee($product2->name); // 部分一致しない商品が表示されないことを確認

    // マイリストページに遷移
    $response = $this->get(route('products.index', ['page' => 'mylist', 'search' => '商品名A']));

    // マイリストページで検索結果が保持されていることを確認
    $response->assertSee($product1->name);
    $response->assertDontSee($product2->name); // 部分一致しない商品が表示されないことを確認
  }

  /**
     * 商品出品画面で情報が正しく保存されることを確認するテスト
     *
     * @return void
     */
    // public function test_product_information_is_saved_correctly()
    // {
    //     // 1. 商品情報を用意
    //     $productData = [
    //         'name' => 'Test Product',
    //         'description' => 'This is a test product description.',
    //         'price' => 1000,
    //         'categories' => 'electronics',
    //         'condition' => 'new',
    //     ];
    
    //     // 2. 商品を出品（保存）
    //     $response = $this->post(route('sell.store'), $productData);
    
    //     // 3. データベースに商品が正しく保存されていることを確認
    //     $this->assertDatabaseHas('products', [
    //         'name' => 'Test Product',
    //         'description' => 'This is a test product description.',
    //         'price' => 1000,
    //         'categories' => 'electronics',
    //         'condition' => 'new',
    //     ]);
    // }

















}
