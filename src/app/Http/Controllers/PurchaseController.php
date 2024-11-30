<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * 購入処理
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'payment_method' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        $user->purchases()->attach($product->id, [
            'payment_method' => $request->payment_method,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 購入データの保存
        Purchase::create([
            'user_id' => Auth::id(),
            'product_id' => $request->input('product_id'),
            'payment_method' => $request->input('payment_method'),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building_name' => $request->input('building_name'),
        ]);

        // 購入後にmypage?page=buyにリダイレクト
        return redirect()->route('mypage', ['page' => 'buy'])->with('success', '購入が完了しました！');
    }

    // public function purchase($id)
    // {
    //     // 商品を取得
    //     $product = Product::findOrFail($id);

    //     // purchaseビューにデータを渡す
    //     return view('purchase', compact('product'));
    // }

    public function show($id)
    {
        $product = Product::findOrFail($id); // 商品情報を取得
        $user = Auth::user(); // ログイン中のユーザー

        return view('purchase', compact('product', 'user'));
    }
}
