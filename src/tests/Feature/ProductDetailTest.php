<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Like;
use Database\Factories\LikeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductDetailTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 商品詳細ページに必要な情報が表示されることをテスト
     *
     * @return void
     */
    public function testProductDetailsDisplayRequiredInfo()
{
    // ユーザーを作成
    $user = User::factory()->create();
    
    // 商品と関連データを作成
    $product = Product::factory()->create([
        'user_id' => $user->id,
        'name' => 'サンプル商品',
        'price' => 1000,
        'description' => 'これはサンプル商品の説明です。',
        'condition' => '新品',
        'categories' => json_encode(['カテゴリ1', 'カテゴリ2']),
    ]);
    
    // 商品にコメントを作成
    $comment = Comment::factory()->create([
        'product_id' => $product->id,
        'user_id' => $user->id,
        'content' => '素晴らしい商品！',
    ]);

    // 商品にいいねを作成
    Like::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);

    // 商品詳細ページにアクセス
    $response = $this->get(route('products.show', ['id' => $product->id]));

    // レスポンスの内容を確認
    // dd($response->getContent());  // レスポンスのHTMLを確認する

    // 必要な情報が表示されていることを確認
    $response->assertStatus(200);
    $response->assertSee($product->name);
    $response->assertSee(number_format($product->price));  // 価格をカンマ区切りで確認
    $response->assertSee($product->description);
    $response->assertSee($product->condition);
    
    // カテゴリが表示されているかを確認
    foreach (json_decode($product->categories, true) as $category) {
        $response->assertSee($category);
    }

    // コメント内容とユーザー名を確認
    $response->assertSee($comment->content);
    $response->assertSee($user->name);

    // いいね数とコメント数が表示されていることを確認
    $response->assertSee($product->likes->count());
    $response->assertSee($product->comments->count());
}

public function testMultipleCategoriesAreDisplayed()
{
    // ユーザーを作成
    $user = User::factory()->create();
    
    // 商品と関連データを作成（複数のカテゴリを追加）
    $product = Product::factory()->create([
        'user_id' => $user->id,
        'name' => 'サンプル商品',
        'price' => 1000,
        'description' => 'これはサンプル商品の説明です。',
        'condition' => '新品',
        'categories' => json_encode(['カテゴリ1', 'カテゴリ2', 'カテゴリ3']), // 複数カテゴリを追加
    ]);

    // 商品詳細ページにアクセス
    $response = $this->get(route('products.show', ['id' => $product->id]));

    // レスポンスの内容を確認
    $response->assertStatus(200);
    
    // 複数カテゴリが商品詳細ページに表示されていることを確認
    foreach (json_decode($product->categories, true) as $category) {
        $response->assertSee($category);
    }
}


public function testUserCanLikeProduct()
{
    // ユーザーを作成してログイン
    $user = User::factory()->create();
    $this->actingAs($user); // ログイン

    // 商品と関連データを作成
    $product = Product::factory()->create([
        'user_id' => $user->id,
        'name' => 'サンプル商品',
        'price' => 1000,
        'description' => 'これはサンプル商品の説明です。',
        'condition' => '新品',
        'categories' => json_encode(['カテゴリ1', 'カテゴリ2']),
    ]);

    // いいねがまだされていないことを確認
    $this->assertEquals(0, $product->likes()->count());

    // 商品詳細ページにアクセス
    $response = $this->get(route('products.show', ['id' => $product->id]));

    // レスポンスの内容を確認
    $response->assertStatus(200);

    // いいねアイコンを押下（正しいパラメータを渡す）
    $response = $this->post(route('like.toggle', ['productId' => $product->id]));

    // レスポンスが JSON 形式で返ってくることを確認
    $response->assertStatus(200);  // いいねが成功した場合のステータスコードを確認
    
    // レスポンスの JSON 内容を確認
    $response->assertJson([
        'success' => true,
        'liked' => true,
        'likeCount' => 1
    ]);

    // いいねが登録され、合計値が増加したことを確認
    $this->assertEquals(1, $product->likes()->count());
}



public function testLikeIconChangesColorWhenLiked()
{
    // テスト用ユーザーと商品を作成
    $user = User::factory()->create();
    $product = Product::factory()->create();

    // ユーザーにログイン
    $this->actingAs($user);

    // 商品詳細ページを開く
    $response = $this->get(route('products.show', ['id' => $product->id]));

    // 初期状態でのアイコンのsrc（色が変化する前）
    $initialIconSrc = 'http://localhost/img/star.svg';

    // アイコンが最初に "star" アイコンであることを確認
    $response->assertSee($initialIconSrc);

    // いいねアイコンを押下して「いいね」状態にする
    $this->post(route('like.toggle', ['productId' => $product->id]));

    // アイコンの色（画像）が変化していることを確認
    $response = $this->get(route('products.show', ['id' => $product->id]));

    // アイコンが "star-filled" に変わったことを確認
    $response->assertSee('http://localhost/img/star-filled.svg');
}

public function testLikeCanBeToggled()
{
    // テスト用ユーザーと商品を作成
    $user = User::factory()->create();
    $product = Product::factory()->create();

    // 初期の「いいね」数を取得
    $initialLikeCount = Like::where('product_id', $product->id)->count();

    // ユーザーにログイン
    $this->actingAs($user);

    // 商品詳細ページを開く
    $response = $this->get(route('products.show', ['id' => $product->id]));

    // 最初の「いいね」を押下
    $this->post(route('like.toggle', ['productId' => $product->id]));

    // いいね数が増加していることを確認
    $this->assertEquals($initialLikeCount + 1, Like::where('product_id', $product->id)->count());

    // 再度いいねアイコンを押下（いいねを解除する）
    $this->post(route('like.toggle', ['productId' => $product->id]));

    // いいね数が減少していることを確認
    $this->assertEquals($initialLikeCount, Like::where('product_id', $product->id)->count());
}








    public function testLoggedInUserCanSubmitComment()
    {
        // 1. ユーザーにログインする
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. 商品詳細ページを開く
        $product = Product::factory()->create();
        $response = $this->get(route('products.show', $product->id));

        // ページが正しく読み込まれたことを確認
        $response->assertStatus(200);

        // 初期状態でコメント数が0であることを確認
        $response->assertSeeText('コメント(0)'); // 初期状態のコメント数

        // 3. コメントを入力する
        $commentContent = 'これはテストコメントです';

        // コメントを送信
        $response = $this->postJson(route('comments.store'), [
            'product_id' => $product->id,
            'content' => $commentContent,
            '_token' => csrf_token(), // CSRFトークンも必要です
        ]);

        // 送信後、コメントが追加されたことを確認
        $response->assertStatus(200); // 正常にリクエストが送信されたことを確認

        // 4. コメント数が1に増加したことを確認
        $response = $this->get(route('products.show', $product->id));
        $response->assertSeeText('コメント(1)'); // コメント数が1に増えていることを確認

        // また、コメントが表示されているか確認
        $response->assertSee($commentContent); // コメント内容が表示されていることを確認
    }

    public function testGuestCannotSubmitComment()
    {
        // 1. ログインしていない状態で商品詳細ページを開く
        $product = Product::factory()->create();
        $response = $this->get(route('products.show', $product->id));
    
        // ページが正しく読み込まれたことを確認
        $response->assertStatus(200);
    
        // 2. コメントを入力する
        $commentContent = 'ログインしていないユーザーのコメント';
    
        // コメントを送信（ログインしていないため、失敗するはず）
        $response = $this->postJson(route('comments.store'), [
            'product_id' => $product->id,
            'content' => $commentContent,
            '_token' => csrf_token(), // CSRFトークンも必要です
        ]);
    
        // 3. コメントが送信されないことを確認
        $response->assertStatus(401); // 未認証のため、401エラーを返すべき
    
        // コメントが送信されていないことを確認（例えば、データベースに保存されていない）
        $this->assertDatabaseMissing('comments', [
            'content' => $commentContent,
            'product_id' => $product->id,
        ]);
    }
    
    public function testValidationMessageIsDisplayedWhenCommentExceedsMaxLength()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create();
        $product = Product::factory()->create();
    
        // ユーザーにログイン
        $this->actingAs($user);
    
        // 商品詳細ページを開く
        $response = $this->get(route('products.show', ['id' => $product->id]));
    
        // 256文字以上のコメントを入力（コメント文字列を生成）
        $longComment = str_repeat('a', 257);  // 257文字のコメント
    
        // コメントの送信
        $response = $this->post(route('comments.store'), [
            'product_id' => $product->id,
            'content' => $longComment,  // 256文字以上のコメント
        ]);
    
        // バリデーションエラーメッセージを確認
        $response->assertSessionHasErrors('content');  // 'content'はコメントフォームのフィールド名と一致させる
    
        // バリデーションメッセージが正しく表示されることを確認
        $response->assertSessionHas('errors');
        $this->assertTrue(session('errors')->has('content'));  // 'content'フィールドにエラーがあるか確認
    }
    


    public function testValidationMessageForLongComment()
    {
        // 1. ユーザーにログインする
        $user = User::factory()->create(); // 必要に応じて適切なユーザー作成方法に変更
        $this->actingAs($user);

        // 2. コメントボタンを押す（256文字以上のコメント）
        $longComment = str_repeat('a', 256); // 256文字のコメントを作成

        $response = $this->post(route('comments.store'), [
            'content' => $longComment,
            'product_id' => 1, // 商品IDを適切に指定
        ]);

        // 3. バリデーションメッセージが表示されることを確認
        $response->assertSessionHasErrors('content'); // content フィールドにエラーがあることを確認
        $this->assertTrue(session('errors')->has('content')); // エラーメッセージが存在することを確認
    }










}
