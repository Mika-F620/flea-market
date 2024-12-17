<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前が入力されていない場合にバリデーションエラーが発生することを確認
     *
     * @return void
     */
    public function test_name_is_required()
    {
        // 名前を空にして、他の必要項目を入力
        $response = $this->post('/register', [
            'name' => '', // 名前は空にする
            'email' => 'test@example.com', // 有効なメールアドレス
            'password' => 'password123', // パスワード
            'password_confirmation' => 'password123', // パスワード確認
        ]);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors('name');

        // 期待されるエラーメッセージが含まれていることを確認
        $this->assertTrue($response->getSession()->has('errors'));
        $this->assertEquals('お名前を入力してください。', $response->getSession()->get('errors')->get('name')[0]);
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => '', // メールアドレスは空
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // メールアドレスが空であるため、'email'にエラーがあることを確認
        $response->assertSessionHasErrors('email');

        // エラーメッセージが「メールアドレスを入力してください。」であることを確認
        $this->assertEquals('メールアドレスを入力してください。', $response->getSession()->get('errors')->get('email')[0]);
    }

    /** @test */
    public function password_must_be_at_least_8_characters()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short', // 7文字以下のパスワード
            'password_confirmation' => 'short',
        ]);

        // パスワードが7文字以下であるため、'password'フィールドにエラーがあることを確認
        $response->assertSessionHasErrors('password');

        // エラーメッセージが「パスワードは8文字以上で入力してください。」であることを確認
        $this->assertEquals('パスワードは8文字以上で入力してください。', $response->getSession()->get('errors')->get('password')[0]);
    }

    /** @test */
    public function password_confirmation_must_match()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword123', // 確認用パスワードが一致しない
        ]);

        // パスワード確認が一致しないため、'password_confirmation'にエラーが発生することを確認
        $response->assertSessionHasErrors(['password']); // 'password'フィールドのエラーが発生することを確認
        $this->assertTrue($response->getSession()->get('errors')->has('password'));
    }

    /** @test */
    public function all_fields_are_filled_and_user_is_registered_and_redirected_to_login_page()
{
    $response = $this->post(route('register'), [
        'name' => 'Test User', // 正しい名前
        'email' => 'testuser@example.com', // 正しいメールアドレス
        'password' => 'password123', // 8文字以上のパスワード
        'password_confirmation' => 'password123', // パスワード確認用
    ]);

    // ユーザーが作成され、email/verifyページにリダイレクトされることを確認
    $response->assertRedirect(route('verification.notice')); // ここを修正

    // 作成されたユーザーがデータベースに保存されていることを確認
    $this->assertDatabaseHas('users', [
        'email' => 'testuser@example.com',
        'name' => 'Test User',
    ]);
}
}
