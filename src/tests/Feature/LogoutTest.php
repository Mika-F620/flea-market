<?php

// tests/Feature/LogoutTest.php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LogoutTest extends TestCase
{
  use DatabaseTransactions;

  /**
   * ログアウト処理が正常に実行されることをテスト
   *
   * @return void
   */
  public function testLogout()
  {
    // ユーザーを作成
    $user = User::factory()->create([
      'email' => 'testuser@example.com',
      'password' => Hash::make('password123'),
    ]);

    // ユーザーにログイン
    $this->actingAs($user);

    // ログアウト処理を実行
    $response = $this->post('/logout');

    // ログアウト後にリダイレクトが行われることを確認
    $response->assertRedirect('/'); // ここはログアウト後のリダイレクト先に合わせて変更できます。

    // ログアウト後にユーザーが認証されていないことを確認
    $this->assertGuest(); // ログアウトしていることを確認
  }
}