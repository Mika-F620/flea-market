<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * 商品の登録処理
     */
    public function store(Request $request)
    {
        $request->merge([
            'price' => str_replace('¥', '', $request->input('price')) // ¥をサーバー側でも削除
        ]);

        $request->validate([
            'image' => ['nullable', 'image', 'max:2048'], // 画像は任意
            'categories' => ['required', 'array'], // カテゴリーは配列で必須
            'categories.*' => ['string', 'max:255'], // 配列内の要素を文字列としてバリデーション
            'condition' => ['required', 'string', 'max:255'], // 商品の状態
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:1'],
        ]);

        // 画像を保存
        $imagePath = $request->hasFile('image') 
            ? $request->file('image')->store('product_images', 'public') 
            : null;

        // データを保存
        Product::create([
            'user_id' => Auth::id(),
            'image' => $imagePath,
            'categories' => json_encode($request->input('categories')), // 配列をJSON形式で保存
            'condition' => $request->input('condition'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
        ]);

        // /mypage?page=sell にリダイレクト
        return redirect()->route('mypage', ['page' => 'sell']);
    }

    /**
     * 商品一覧の表示
     */
    public function index()
    {
        $products = Product::with('user')->latest()->get();
        return view('products.index', compact('products'));
    }
}
