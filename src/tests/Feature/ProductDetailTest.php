<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductDetailTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 商品詳細ページに必要な情報が表示されることをテスト
     *
     * @return void
     */
    // public function testRequiredInformationIsDisplayedOnProductDetailPage()
    // {
    //     // ユーザーを作成してログイン
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // 商品を作成
    //     $product = Product::factory()->create([
    //         'name' => 'テスト商品',
    //         'image' => 'dummy_image.jpg',
    //         'description' => '商品説明です。',
    //         'price' => 5000,
    //         'condition' => '新品',
    //     ]);

    //     // ブランド名を設定（仮にbrand_nameとして）
    //     $product->brand_name = 'テストブランド';
    //     $product->save();

    //     // いいね数を作成
    //     $product->likes()->create([
    //         'user_id' => $user->id,
    //     ]);

    //     // コメントを作成
    //     $comment = Comment::create([
    //         'user_id' => $user->id,
    //         'product_id' => $product->id,
    //         'content' => 'コメント内容です。',
    //     ]);

    //     // 商品詳細ページにアクセス
    //     $response = $this->get(route('products.show', $product->id));

    //     // 商品ページに必要な情報が表示されていることを確認
    //     $response->assertSee($product->name);  // 商品名
    //     $response->assertSee($product->brand_name);  // ブランド名
    //     $response->assertSee($product->description);  // 商品説明
    //     $response->assertSee('¥' . number_format($product->price));  // 価格
    //     $response->assertSee($product->condition);  // 商品状態
    //     $response->assertSee('いいね数: 1');  // いいね数
    //     $response->assertSee('コメント数: 1');  // コメント数
    //     $response->assertSee($comment->content);  // コメント内容
    //     $response->assertSee($user->name);  // コメントしたユーザー名
    // }

    // public function testCategoriesAreDisplayedOnProductDetailPage()
    // {
    //     // 1. ユーザーを作成してログイン
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // 2. 複数のカテゴリを持つ商品を作成
    //     $product = Product::create([
    //         'name' => 'Test Product',
    //         'description' => 'Test Description',
    //         'price' => 1000,
    //         'user_id' => $user->id,
    //         'image' => 'dummy_image.jpg',
    //         'condition' => 'new',
    //         'categories' => json_encode(['カテゴリー1', 'カテゴリー2']), // 複数カテゴリ
    //     ]);

    //     // 3. 商品詳細ページを開く
    //     $response = $this->get(route('products.show', ['product' => $product->id]));

    //     // 4. 商品詳細ページにカテゴリが表示されているか確認
    //     $response->assertSee('カテゴリー'); // カテゴリの見出し
    //     $response->assertSee('カテゴリー1'); // 複数カテゴリが表示されているか
    //     $response->assertSee('カテゴリー2');
    // }

//     public function testUserCanLikeProductAndLikeCountIncreases()
// {
//     // ユーザーを作成してログイン
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 商品を作成
//     $product = Product::factory()->create();

//     // 商品詳細ページを開く
//     $response = $this->get(route('products.show', $product->id));

//     // 商品詳細ページに「いいね」アイコンが存在することを確認（エスケープされた文字列で一致する）
//     $response->assertSeeText('id="like-icon"');

//     // 商品のいいね数を記録
//     $initialLikeCount = $product->likes->count();

//     // いいねアイコンをクリックする（JavaScriptで非同期処理が行われる場合も想定してPOSTリクエストを送る）
//     $response = $this->post(route('like.toggle', $product->id));

//     // いいねが正常に登録され、いいね数が増加したことを確認
//     $response->assertStatus(200);  // 正常なレスポンスであることを確認

//     // 商品のいいね数が増加したことを確認
//     $this->assertDatabaseHas('likes', [
//         'user_id' => $user->id,
//         'product_id' => $product->id
//     ]);

//     // 商品詳細ページでいいね数が増加したことを確認
//     $response = $this->get(route('products.show', $product->id));
//     $response->assertSee((string) ($initialLikeCount + 1));  // いいね数が1増えたことを確認
// }

// public function testLikeIconChangesColor()
// {
//     // 1. ユーザーにログインする
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 2. 商品詳細ページを開く
//     $product = Product::factory()->create();
//     $response = $this->get(route('products.show', $product->id));

//     // ページが正しく読み込まれたことを確認
//     $response->assertStatus(200);

//     // 初期状態で「いいね」アイコンの表示を確認（エスケープされた状態）
//     $response->assertSeeText('&lt;img alt=&quot;星&quot; src=&quot;http://localhost/img/star.svg&quot;');

//     // いいねボタンをクリック（通常はAJAXリクエストでいいね状態を更新）
//     $this->postJson(route('like.toggle', $product->id), ['like' => true]);

//     // ページを再読み込みして、アイコンが色が変わっていることを確認（エスケープされた状態）
//     $response = $this->get(route('products.show', $product->id));
//     $response->assertSeeText('&lt;img alt=&quot;星&quot; src=&quot;http://localhost/img/star-filled.svg&quot;');
// }


// public function testLikeIconCanBeToggled()
// {
//     // 1. ユーザーにログインする
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     // 2. 商品詳細ページを開く
//     $product = Product::factory()->create();
//     $response = $this->get(route('products.show', $product->id));

//     // ページが正しく読み込まれたことを確認
//     $response->assertStatus(200);

//     // 初期状態でいいねアイコンが「未いいね」の状態で表示されることを確認
//     $response->assertSee('src="http://localhost/img/star.svg"'); // 初期状態で未いいねアイコンの確認

//     // 3. いいねアイコンを押下（いいねを付ける）
//     $this->postJson(route('like.toggle', $product->id), ['like' => true]);

//     // いいねが追加された後、合計いいね数が増加したことを確認
//     $response = $this->get(route('products.show', $product->id));
//     $response->assertSeeText('1'); // いいね合計数が1になっていることを確認

//     // 4. 再度いいねアイコンを押下（いいねを解除する）
//     $this->postJson(route('like.toggle', $product->id), ['like' => false]);

//     // いいねが解除された後、合計いいね数が減少したことを確認
//     $response = $this->get(route('products.show', $product->id));
//     $response->assertSeeText('0'); // いいね合計数が0になっていることを確認
// }

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
    
//     public function testValidationMessageForLongComment()
// {
//     // 1. ユーザーにログイン
//     $user = User::factory()->create();  // ユーザーを作成
//     $this->actingAs($user);  // ログイン状態にする

//     // 商品を作成
//     $product = Product::factory()->create();

//     // 2. 256文字以上のコメントを入力する
//     $longComment = str_repeat('a', 257);  // 257文字以上のコメント

//     // 3. コメントボタンを押す
//     $response = $this->postJson(route('comments.store'), [
//         'product_id' => $product->id,
//         'content' => $longComment,
//         '_token' => csrf_token(), // CSRFトークンも必要
//     ]);

//     // 4. バリデーションメッセージが表示されることを確認
//     $response->assertStatus(422); // バリデーションエラーで422エラー
//     $response->assertJsonValidationErrors(['content']);  // 'content'フィールドにエラーがあることを確認
//     $response->assertJson([
//         'message' => 'The content field must be less than 255 characters.'
//     ]); // バリデーションエラーメッセージが返っていることを確認
// }


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
