<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // User モデルをインポート

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

        // ログイン状態にする
        auth()->login($user);

        // ログイン後のリダイレクト先
        return redirect()->route('home')->with('success', '登録が完了しました！');
    }
}
