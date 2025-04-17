<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\StripePaymentsController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ChatController;
use App\Models\TradingProduct;
use App\Models\Product;

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

Route::get('/', [ProductController::class, 'index'])->name('products.index');

Route::get('/mypage', [ProductController::class, 'mypage'])->name('mypage');

// ログインフォームの表示
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('login');

// ログイン処理
Route::post('/login', [AuthController::class, 'login'])->name('login');

// ログアウト処理
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

// メール認証用
Route::get('email/verify', function () {
    return view('auth.verify'); // メール認証画面
})->name('verification.notice');

Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

Route::middleware(['auth', 'verified'])->get('/mypage/profile', [ProfileController::class, 'show'])->name('mypage.profile');

// 認証後、アクセスするページ
Route::get('home', [HomeController::class, 'index'])->middleware('verified')->name('home');

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

Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.store');

Route::get('/purchase/address/{id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
Route::post('/purchase/address/{id}', [AddressController::class, 'update'])->name('purchase.address.update');
Route::put('/purchase/address/{id}', [AddressController::class, 'update'])->name('purchase.address.update');

Route::post('/like/{productId}', [LikeController::class, 'store'])->name('like.store');
Route::delete('/like/{product}', [LikeController::class, 'destroy'])->middleware('auth')->name('like.destroy');
Route::post('/like/toggle/{productId}', [LikeController::class, 'toggleLike'])->name('like.toggle');

Route::middleware(['auth'])->group(function () {
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
});

Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// 商品購入ページ（商品情報を表示するページ）
Route::get('/purchase/{id}', [PurchaseController::class, 'show'])->name('purchase.show'); 

// Stripe決済ページ（決済処理）
Route::post('/payment', [PurchaseController::class, 'payment'])->name('payment.store');

// 決済成功後に表示するサンクスページ（completeページ）
Route::get('/payment/success', [PurchaseController::class, 'success'])->name('payment.success');

// 完了ページ
Route::get('/complete', [PurchaseController::class, 'complete'])->name('complete');

Route::post('products/trading/{id}', [ProductController::class, 'trading'])->name('products.trading');

// チャット画面を表示するルート
Route::get('chat/show/{id}', [ProductController::class, 'showChat'])->name('chat.show');

// チャット表示
// Route::get('/chat/{receiver_id}', [ChatController::class, 'show'])->name('chat.show');

// Route::get('chat/show/{product_id}', [ChatController::class, 'show'])->name('chat.show');

Route::get('/chat/{product_id}', [ChatController::class, 'show'])->name('chat.show');


// チャットメッセージ送信
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

// 編集ページ表示
Route::get('/chat/edit/{message_id}', [ChatController::class, 'editMessagePage'])->name('chat.edit');

// メッセージ更新
Route::post('/chat/edit/{message_id}', [ChatController::class, 'editMessage'])->name('chat.edit');



// メッセージ削除用のルート
Route::delete('/chat/delete/{message_id}', [ChatController::class, 'deleteMessage'])->name('chat.delete');

