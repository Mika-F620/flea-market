<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SellRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Like;
use App\Models\TradingProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $page = $request->query('page', 'recommend');
        $user = Auth::user();
        $searchQuery = $request->query('search', '');

        if ($page === 'mylist') {

            if (!$user) {
                return redirect()->route('login')->with('error', 'ログインが必要です。');
            }
            
            // ユーザーがいいねした商品の中で、出品者が出品した商品を除外し、検索クエリが一致するものを取得
            $products = Product::where('user_id', '!=', $user->id)  // 出品者が出品した商品は除外
            ->whereIn('id', $user->likes()->pluck('product_id'))  // ユーザーが「いいね」した商品のみ
            ->when($searchQuery, function ($query) use ($searchQuery) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');  // 検索クエリがある場合
            })
            ->get();
        } elseif ($page === 'trading') {
            // 「取引中の商品」タブが選択された場合
            $products = TradingProduct::where('user_id', $user->id)->get();  // ログイン中のユーザーが取引中の商品
        } else {
            // 全商品の中で検索クエリが一致するものを取得
            $products = Product::query()
                ->when(!empty($searchQuery), function ($query) use ($searchQuery) {
                    $query->where('name', 'LIKE', '%' . $searchQuery . '%');
                })
                ->when($user, function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id); // ログインユーザーの出品商品を除外
                })
                ->latest()
                ->get();
        }

        // 購入済みフラグを設定
        foreach ($products as $product) {
            $product->is_sold = Purchase::where('product_id', $product->id)->exists();
        }

        return view('index', compact('products', 'page', 'searchQuery'));
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

        // `page` のデフォルト値を設定
        $page = 'recommend';

        // 検索クエリをデフォルト値として渡す
        $searchQuery = request()->query('search', '');

        return view('item', compact('product', 'isLiked', 'likeCount', 'comments', 'user', 'page', 'searchQuery'));
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

    public function trading($id)
    {
        // 商品を取得
        $product = Product::findOrFail($id);

        // ログインユーザーを取得
        $user = Auth::user();

        // 商品がすでに取引中のテーブルに存在しない場合に保存
        TradingProduct::create([
            'product_id' => $product->id,  // 商品ID
            'user_id' => $user->id,        // 出品者ID
            'name' => $product->name,      // 商品名
            'image' => $product->image,    // 商品画像
            'price' => $product->price,    // 料金
            'status' => '取引中',          // 取引中の状態
        ]);

        // 取引中商品一覧ページにリダイレクト
        return redirect()->route('products.index', ['page' => 'trading'])->with('success', '商品が取引中に移動しました');
    }

    public function showChat($id)
    {
        // 取引中の商品を取得
        $tradingProduct = TradingProduct::findOrFail($id);

        // 商品の情報を取得
        $product = $tradingProduct->product;
        $seller = $product->user; // 出品者
        $buyer = Auth::user(); // 現在ログインしているユーザー（取引相手）

        return view('chat.show', compact('tradingProduct', 'product', 'seller', 'buyer'));
    }
}