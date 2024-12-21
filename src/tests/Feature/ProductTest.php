<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


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

  /**
   * 購入済み商品は「Sold」と表示されるテスト
   *
   * @return void
   */
  public function testSoldProductHasSoldLabel()
  {
    // 商品を作成
    $product = Product::factory()->create([
      'name' => 'Sample Product',
      'price' => 1000,
      'image' => 'dummy_image.jpg', // ダミー画像
    ]);

    // 購入したユーザーを作成
    $user = User::factory()->create();

    // 購入レコードを作成して、商品を購入済みにする
    $user->purchases()->attach($product->id, ['payment_method' => 'コンビニ払い']);  // payment_methodを指定

    // 商品一覧ページにアクセス
    $response = $this->get(route('products.index')); // 商品一覧ページにアクセス

    // 商品がページに表示されていることを確認
    $response->assertSee($product->name);

    // 商品が「Sold」と表示されていることを確認
    $response->assertSee('Sold'); // 「Sold」のラベルが表示されていることを確認
  }

  /**
   * 自分が出品した商品は表示されないテスト
   *
   * @return void
   */
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

  /**
   * いいねした商品だけが表示されるテスト
   *
   * @return void
   */
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

  /**
   * 購入済み商品は「Sold」と表示されるテスト
   *
   * @return void
   */
  public function testSoldProductHasSoldLabelInMyList()
  {
    // ユーザーを作成してログイン
    $user = User::factory()->create();
    $this->actingAs($user);

    // 商品を作成
    $product = Product::factory()->create([
      'name' => 'Sample Product',
      'price' => 1000,
      'condition' => '新品',
    ]);

    // ユーザーが商品を「いいね」する（likesテーブルにレコードを追加）
    $user->likes()->create(['product_id' => $product->id]);

    // 商品を購入済みとして関連付け
    $purchase = Purchase::create([
      'user_id' => $user->id,
      'product_id' => $product->id,
      'payment_method' => 'カード払い',
    ]);

    // 商品が「Sold」と表示されているかを確認
    $response = $this->get(route('products.index'));  // ここでのルートが「マイリスト」に相当する場合
    $response->assertSee('Sold');
  }

  /**
   * 自分が出品した商品は表示されないテスト
   *
   * @return void
   */
  public function testProductDoesNotShowUpInMyListForUserWhoListedIt()
  {
    // ログインするユーザーを作成
    $user = User::factory()->create();
    $this->actingAs($user);  // ログイン

    // ユーザーが出品した商品
    $product = Product::factory()->create([
      'user_id' => $user->id, // 出品者としてログインユーザーを設定
      'name' => 'Sample Product',
      'price' => 1000,
      'condition' => '新品',
    ]);

    // ユーザーがその商品を「いいね」する
    $user->likes()->create(['product_id' => $product->id]);

    // マイリストページにアクセス
    $response = $this->get('/?page=mylist&search=');

    // 出品者が出品した商品はマイリストに表示されないことを確認
    $response->assertDontSee($product->name);  // 出品者が出品した商品は表示されない

    // 他のユーザーが出品した商品
    $anotherUser = User::factory()->create();
    $likedProduct = Product::factory()->create([
      'user_id' => $anotherUser->id, // 異なるユーザーが出品した商品
      'name' => 'Liked Product',
      'price' => 500,
      'condition' => '新品',
    ]);

    // ユーザーがその商品を「いいね」する
    $user->likes()->create(['product_id' => $likedProduct->id]);

    // 再度マイリストページにアクセス
    $response = $this->get('/?page=mylist&search=');

    // いいねした商品は表示されることを確認
    $response->assertSee($likedProduct->name);  // ユーザーが「いいね」した商品は表示される
  }

  /**
   * 未認証の場合は何も表示されないテスト
   *
   * @return void
   */
  public function testNothingIsDisplayedForUnauthenticatedUser()
  {
    // 未認証状態でマイリストページにアクセス
    $response = $this->get(route('products.index', ['page' => 'mylist']));

    // ログインしていない場合、ログインページにリダイレクトされることを確認
    $response->assertRedirect(route('login')); // ログインページにリダイレクトされることを確認

    // リダイレクト先のURLを確認する
    $response->assertStatus(302); // 302リダイレクトステータスコードを確認
  }

  /**
   * 「商品名」で部分一致検索ができるテスト
   *
   * @return void
   */
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

  /**
   * 検索状態がマイリストでも保持されているテスト
   *
   * @return void
   */
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
}