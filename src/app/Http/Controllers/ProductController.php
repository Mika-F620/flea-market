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

    public function show($id)
    {
        // 商品を取得（idを元に取得）
        $product = Product::findOrFail($id); // 該当する商品がなければ404エラーを返す

        // ビューにデータを渡す
        return view('item', compact('product'));
    }

    public function purchase(Request $request)
    {
        // 購入のバリデーション
        $request->validate([
            'product_id' => ['required', 'exists:products,id'], // 商品が存在するか確認
            'payment_method' => ['required', 'string'],        // 支払い方法
        ]);

        // 購入データの保存
        Purchase::create([
            'user_id' => Auth::id(),
            'product_id' => $request->input('product_id'),
            'payment_method' => $request->input('payment_method'),
        ]);

        // /mypage?page=buy にリダイレクト
        return redirect()->route('mypage', ['page' => 'buy'])->with('success', '購入が完了しました！');
    }

    public function showPurchase(Product $product)
    {
        // ログインしているユーザー情報を取得
        $user = Auth::user();

        // 商品情報とユーザー情報をビューに渡す
        return view('purchase', compact('product', 'user'));
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        // 出品した商品
        if ($page === 'sell') {
            $products = Product::where('user_id', $user->id)->get();
            \Log::info('出品商品: ' . $products->toJson());
        }
        // 購入した商品
        elseif ($page === 'buy') {
            $products = $user->purchasedProducts()->latest()->get(); // リレーションを使用
        } else {
            $products = collect(); // 空のコレクション
        }

        // デバッグ
        \Log::info('Page: ' . $page);
        \Log::info('User: ' . $user->id);
        \Log::info('Products: ' . $products->toJson());

        return view('mypage', compact('user', 'page', 'products'));
    }
}