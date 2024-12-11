<?php

// tests/Feature/LoginTest.php
namespace Tests\Feature;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginTest extends TestCase
{
  // use RefreshDatabase;
  // use DatabaseTransactions;
  use DatabaseTransactions;

  /**
   * メールアドレスが入力されていない場合、バリデーションメッセージが表示される
   *
   * @return void
   */
  public function testEmailIsRequiredForLogin()
  {
    // ログインページにアクセス
    $response = $this->get('/login');

    // ログインフォームが表示されることを確認
    $response->assertStatus(200);
    
    // メールアドレスが空で、他の項目が入力されている場合にエラーメッセージが表示されることを確認
    $response = $this->post('/login', [
      'login_identifier' => '', // メールアドレスは空
      'password' => 'password123' // 適当なパスワード
    ]);

    // エラーがセッションに追加されていることを確認
    $response->assertSessionHasErrors(['login_identifier']); // 'login_identifier' フィールドにエラーがあるか

    // メッセージがセッションに含まれていることを確認
    $errors = session('errors');
    $this->assertEquals('ユーザー名またはメールアドレスを入力してください。', $errors->get('login_identifier')[0]);
  }

  /**
   * パスワードが入力されていない場合、バリデーションメッセージが表示される
   *
   * @return void
   */
  public function testPasswordIsRequiredForLogin()
  {
    // ログインページにアクセス
    $response = $this->get('/login');

    // ログインフォームが表示されることを確認
    $response->assertStatus(200);
    
    // パスワードが空で、他の項目が入力されている場合にエラーメッセージが表示されることを確認
    $response = $this->post('/login', [
      'login_identifier' => 'test@example.com', // メールアドレス（仮のもの）
      'password' => '' // パスワードは空
    ]);

    // エラーがセッションに追加されていることを確認
    $response->assertSessionHasErrors(['password']); // 'password' フィールドにエラーがあるか

    // メッセージがセッションに含まれていることを確認
    $errors = session('errors');
    $this->assertEquals('パスワードを入力してください。', $errors->get('password')[0]);
  }

  /**
   * 入力情報が間違っている場合、バリデーションメッセージが表示される
   *
   * @return void
   */
  public function testInvalidLoginInformation()
  {
    // テスト用ユーザーを作成
    $user = User::factory()->create([
      'email' => 'test@example.com',
      'password' => Hash::make('password123') // 適切なパスワードを設定
    ]);

    // ログインページにアクセス
    $response = $this->get('/login');

    // ログインフォームが表示されることを確認
    $response->assertStatus(200);
    
    // 間違ったメールアドレスとパスワードでログインを試みる
    $response = $this->post('/login', [
      'login_identifier' => 'wrongemail@example.com', // 登録されていないメールアドレス
      'password' => 'wrongpassword' // 間違ったパスワード
    ]);

    // バリデーションエラーメッセージがセッションに含まれていることを確認
    $response->assertSessionHasErrors('login_identifier'); // 'login_identifier' フィールドにエラーがあるか

    // メッセージがセッションに含まれていることを確認
    $errors = session('errors');
    $this->assertEquals('ログイン情報が登録されていません。', $errors->get('login_identifier')[0]);
  }

  /**
   * 正しい情報が入力された場合、ログイン処理が実行される
   *
   * @return void
   */
  public function testLoginWithValidCredentials()
  {
    // ユーザーを作成
    $user = User::factory()->create([
      'email' => 'testuser@example.com',
      'password' => Hash::make('password123'),
    ]);

    // ログイン処理
    $response = $this->post('/login', [
      'login_identifier' => $user->email,
      'password' => 'password123',
    ]);

    // ログイン後、リダイレクトステータス302が返されることを確認
    $response->assertStatus(302);

    // リダイレクト先がルート ('/') であることを確認
    $response->assertRedirect('/');

    // このユーザーが認証されていることを確認
    $this->assertAuthenticatedAs($user);
  }
}
