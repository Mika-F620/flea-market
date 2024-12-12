<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    public function __construct()
    {
        // show と store メソッドには認証ミドルウェアを適用
        $this->middleware('auth')->only(['show', 'store']);
    }

    // 商品購入ページ（決済画面）
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

    // 購入処理：購入データ保存
    public function store(PurchaseRequest $request)
    {
        // ユーザー情報
        $user = Auth::user();

        // バリデーションは自動的に行われます（PurchaseRequestに定義されたルール）
        $validatedData = $request->validated(); // バリデーション済みデータ

        // 商品IDをリクエストから取得
        $productId = $validatedData['product_id'];
        $product = Product::findOrFail($productId);

        // 住所情報（バリデーション済みのデータを使う）
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

        // 購入データの保存
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'payment_method' => $validatedData['payment_method'],
            'postal_code' => $postalCode,
            'address' => $address,
            'building_name' => $buildingName,
            // 'amount' => $product->price,
        ]);

        // セッションから一時的な住所情報を削除
        session()->forget('temp_address');

        // 支払い方法をセッションに保存
        session(['payment_method' => $validatedData['payment_method']]);

        // 決済処理へリダイレクト
        return redirect()->route('payment.store', ['product_id' => $product->id]); // 決済処理にリダイレクト
    }

    // Stripe決済ページ（決済処理）
public function payment(PurchaseRequest $request)
{
    try {
        // 商品IDをリクエストから取得
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);

        // StripeのAPIキーを設定
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // リクエストから購入情報を直接取得してセッションに保存
        session([
            'product_id' => $request->input('product_id'),
            'payment_method' => $request->input('payment_method'),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building_name' => $request->input('building_name'),
        ]);

        // Stripe Checkoutセッションの作成
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $product->name, // 商品名
                        ],
                        'unit_amount' => $product->price, // 金額（セント単位）
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['product_id' => $product->id]), // 決済成功後に遷移するURL
            'cancel_url' => route('purchase.show', $product->id), // キャンセル時に遷移するURL
        ]);

        // Stripe Checkoutページにリダイレクト
        return redirect()->away($session->url);

    } catch (\Exception $e) {
        return back()->withErrors(['error' => '決済処理に失敗しました。再試行してください。']);
    }
}


    // 決済成功後のサンクスページ
public function success(Request $request)
{
    // セッションから購入情報を取得
    $productId = session('product_id');
    $paymentMethod = session('payment_method');
    $postalCode = session('postal_code');
    $address = session('address');
    $buildingName = session('building_name');
    
    // 商品情報を取得
    $product = Product::findOrFail($productId);

    // ユーザー情報を取得
    $user = Auth::user();

    // 購入データの保存
    Purchase::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'payment_method' => $paymentMethod,
        'postal_code' => $postalCode,
        'address' => $address,
        'building_name' => $buildingName,
    ]);

    // セッションのデータを削除
    session()->forget(['product_id', 'payment_method', 'postal_code', 'address', 'building_name']);

    // 完了ページを表示
    return view('complete');
}


    // 完了ページ
    public function complete()
    {
        return view('complete'); // 完了ページ（サンクスページ）
    }
}
