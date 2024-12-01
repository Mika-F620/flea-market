<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SellRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * 商品の登録処理
     */
    public function store(SellRequest $request)
    {
        // バリデーションが通過した後、画像の価格を処理
        $validated = $request->validated();
        $request->merge([
            'price' => str_replace('¥', '', $request->input('price')) // ¥をサーバー側でも削除
        ]);

        // $request->validate([
        //     'image' => ['nullable', 'image', 'max:2048'], // 画像は任意
        //     'categories' => ['required', 'array'], // カテゴリーは配列で必須
        //     'categories.*' => ['string', 'max:255'], // 配列内の要素を文字列としてバリデーション
        //     'condition' => ['required', 'string', 'max:255'], // 商品の状態
        //     'name' => ['required', 'string', 'max:255'],
        //     'description' => ['required', 'string'],
        //     'price' => ['required', 'integer', 'min:1'],
        // ]);

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
    public function index(Request $request)
    {
        // クエリパラメータでページ種別を取得、デフォルトは 'recommend'
        $page = $request->query('page', 'recommend'); 
        $user = Auth::user(); // ログインユーザーの取得
        $products = collect(); // 空のコレクションを初期化

        if ($page === 'mylist') {
            // マイリスト表示の場合
            if ($user) {
                // ユーザーのお気に入り商品を取得
                $products = Like::where('user_id', $user->id)
                    ->with('product.user') // 商品とその投稿者情報を取得
                    ->get()
                    ->pluck('product'); // 商品データのみ抽出
            } else {
                // ログインしていない場合、ログイン画面にリダイレクト
                return redirect()->route('login')->with('error', 'マイリストを表示するにはログインが必要です。');
            }
        } else {
            // デフォルト (recommend) は全商品の中から最新20件を取得
            $products = Product::with('user')->latest()->take(20)->get();
        }

        // 商品が購入済みかどうかを判定して、それをビューに渡す
        foreach ($products as $product) {
            $product->is_sold = Purchase::where('product_id', $product->id)->exists();
        }

        // 適切なビューを返す
        return view('index', compact('products', 'page'));
    }

    public function show($id)
    {
        $product = Product::with(['comments.user', 'likes'])->findOrFail($id);
        $user = Auth::user();

        // いいねの初期状態を取得
        $isLiked = $user ? $product->likes()->where('user_id', $user->id)->exists() : false;

        // いいねの合計数を取得
        $likeCount = $product->likes()->count();

        // コメントの取得（関連モデルを使用）
        $comments = $product->comments()->with('user')->get();

        return view('item', compact('product', 'isLiked', 'likeCount', 'comments', 'user'));
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
        // ログイン状態を確認
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        // ログイン中のユーザーを取得
        $user = Auth::user();
        $page = $request->query('page', 'sell'); // デフォルトで 'sell'

        // 出品商品か購入商品を取得
        if ($page === 'sell') {
            $products = Product::where('user_id', $user->id)->get();
            // 出品した商品が購入されたかどうか確認
            foreach ($products as $product) {
                // 商品が購入されたかどうかを判定
                $product->is_sold = Purchase::where('product_id', $product->id)->exists();
            }
        } elseif ($page === 'buy') {
            // $products = $user->purchasedProducts()->latest()->get(); // 購入した商品
            // 購入済み商品を取得
            $products = Purchase::where('user_id', $user->id)
            ->with('product') // 購入商品情報を取得
            ->get()
            ->pluck('product'); // 購入商品データのみ抽出
            } else {
                $products = collect(); // 空のコレクション
            }

        return view('mypage', compact('user', 'page', 'products'));
    }
}
