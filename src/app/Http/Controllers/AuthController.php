<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // User モデルをインポート
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     * 新規登録フォームを表示
     */
    public function showRegisterForm()
    {
        return view('auth.register'); // resources/views/auth/register.blade.php
    }

    /**
     * 新規登録処理
     */
    public function register(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'], // confirmed: パスワード確認フィールド
        ]);

        // ユーザーの作成
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // パスワードをハッシュ化
        ]);

        // メール認証用イベントを発行
        event(new Registered($user));

        // ユーザーにメール認証リンクを送信後、認証通知ページにリダイレクト
        return redirect()->route('verification.notice');
    }

    public function sendTestEmail()
    {
        Mail::raw('This is a test email sent through Mailhog!', function ($message) {
            $message->to('recipient@example.com')->subject('Test Email from Mailhog');
        });

        return 'Test email sent!';
    }

    /**
     * メール認証処理
     */
    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        // ユーザーがまだ認証されていない場合
        if ($user->hasVerifiedEmail()) {
            // すでに認証済みの場合は、mypage/profileにリダイレクト
            return redirect()->route('mypage.profile');
        }

        // メール認証をマークしてからログイン
        if ($user->markEmailAsVerified()) {
            // 自動でユーザーをログインさせる
            Auth::login($user);

            // 認証後、mypage/profileにリダイレクト
            return redirect('mypage/profile');
        }

        // メール認証が失敗した場合
        return redirect()->route('verification.notice');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('login_identifier', 'password');

        // ユーザー名またはメールアドレスでユーザーを検索
        $user = User::where('email', $credentials['login_identifier'])
                    ->orWhere('name', $credentials['login_identifier'])
                    ->first();

        if ($user) {
            // ユーザーが存在し、パスワードが一致する場合
            if (Auth::attempt(['email' => $user->email, 'password' => $credentials['password']])) {

                // メール認証されていない場合、ログインを拒否
                if (!$user->hasVerifiedEmail()) {
                    Auth::logout();  // 未認証の場合はログアウト
                    return redirect()->route('login')->withErrors(['login_error' => '未認証ユーザーです。認証メールを確認してください。']);
                }

                // 認証されたユーザーの場合、トップページにリダイレクト
                return redirect('/');  // ここでトップページに遷移
            }
        }

        // 資格情報が一致しない場合のエラーメッセージ
        return back()->withErrors([
            'login_identifier' => 'ログイン情報が登録されていません。',
        ]);
    }
}