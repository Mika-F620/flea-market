<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // プロフィール表示
    public function show()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return view('profile');
        }
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    // プロフィール更新
    public function update(Request $request)
    {
        // ログイン中のユーザー
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:8', 'regex:/^\d{3}-\d{4}$/'], // 例: 123-4567
            'address' => ['nullable', 'string', 'max:255'],
            'building_name' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // 画像のバリデーション
        ]);

        // プロフィール画像がアップロードされた場合
        if ($request->hasFile('profile_image')) {
            // 古い画像がある場合削除
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }
        
            // 新しい画像を保存
            $path = $request->file('profile_image')->store('profile_images', 'public'); // 保存先: storage/app/public/profile_images
            $user->profile_image = $path; // パスをユーザーモデルに保存
        }

        // その他のデータを更新
        $user->name = $request->input('name');
        $user->postal_code = $request->input('postal_code');
        $user->address = $request->input('address');
        $user->building_name = $request->input('building_name');
        $user->save();

        // 更新後に指定ページへリダイレクト
        return redirect('/?page=mylist')->with('success', 'プロフィールを更新しました！');
    }
}