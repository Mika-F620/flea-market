<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

// Route::get('/mypage', function (Illuminate\Http\Request $request) {
//     $user = Auth::user();
//     $page = $request->query('page', 'sell'); // デフォルトを'sell'に設定

//     // ログインしているユーザーの商品を取得
//     if ($page === 'sell') {
//         $products = \App\Models\Product::where('user_id', $user->id)->latest()->get();
//     } elseif ($page === 'buy') {
//         // 購入履歴を取得する処理（購入テーブルがあればここで対応）
//         $products = collect(); // 現時点では空のコレクションとしておく
//     } else {
//         $products = collect(); // 不明なページの場合も空にする
//     }

//     return view('mypage', compact('user', 'products', 'page'));
// })->name('mypage')->middleware('auth');

Route::get('/mypage', [ProductController::class, 'mypage'])->name('mypage');


// ログインフォームの表示
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('login');

// ログイン処理
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest']);

// ログアウト処理
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// マイページのルート定義
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/home', function () {
    return redirect('/mypage/profile');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::post('/sell', [ProductController::class, 'store'])->name('sell.store'); // 商品登録
    Route::get('/sell', function () {
        return view('sell'); // sell.blade.php を表示
    })->name('sell.index');
});

Route::get('/item/{id}', [ProductController::class, 'show'])->name('item.show');

Route::get('/purchase/{id}', [PurchaseController::class, 'show'])
    ->name('purchase.show')
    ->middleware('auth'); // 認証を強制する
Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.store');

Route::get('/purchase/address/{id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
Route::post('/purchase/address/{id}', [AddressController::class, 'update'])->name('purchase.address.update');
Route::put('/purchase/address/{id}', [AddressController::class, 'update'])->name('purchase.address.update');