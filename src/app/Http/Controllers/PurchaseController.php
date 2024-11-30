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
        $user = Auth::user();

        // バリデーション
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'payment_method' => 'required|string',
            // 'postal_code' => 'nullable|string',
            // 'address' => 'nullable|string',
            // 'building_name' => 'nullable|string',
        ]);

        // セッションまたはユーザー情報を利用して配送先情報を補完
        $tempAddress = session('temp_address', []); // セッション情報を取得
        $postalCode = $validatedData['postal_code'] ?? $tempAddress['postal_code'] ?? $user->postal_code;
        $address = $validatedData['address'] ?? $tempAddress['address'] ?? $user->address;
        $buildingName = $validatedData['building_name'] ?? $tempAddress['building_name'] ?? $user->building_name;

        // 必須フィールドが存在しない場合にエラーメッセージを追加
        if (!$postalCode || !$address) {
            return back()->withErrors([
                'postal_code' => '郵便番号を入力するか、既存の住所を確認してください。',
                'address' => '住所を入力するか、既存の住所を確認してください。',
            ])->withInput();
        }

        $product = Product::findOrFail($request->product_id);

        // $user->purchases()->attach($product->id, [
        //     'payment_method' => $request->payment_method,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // 購入データの保存
        Purchase::create([
            'user_id' => $user->id,
            'product_id' => $validatedData['product_id'],
            'payment_method' => $validatedData['payment_method'],
            'postal_code' => $postalCode,
            'address' => $address,
            'building_name' => $buildingName,
        ]);

        // セッションから一時的な住所情報を削除
        session()->forget('temp_address');

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
        $user = Auth::user();

        // セッションから一時的な配送先情報を取得（変更済みの情報がある場合）
        $tempAddress = session('temp_address', [
            'postal_code' => $user->postal_code,
            'address' => $user->address,
            'building_name' => $user->building_name,
        ]);

        return view('purchase', compact('product', 'user', 'tempAddress'));
    }
}
