<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
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

Route::get('/mypage', function () {
    return view('mypage'); // mypage.blade.php を表示
})->name('mypage');

// Route::get('/login', [AuthenticatedSessionController::class, 'create'])
//     ->middleware('guest') // メソッドチェーンでミドルウェアを追加
//     ->name('login'); // 名前付きルートの定義

// Route::post('/login', [AuthenticatedSessionController::class, 'store'])
//     ->middleware('guest') // メソッドチェーンでミドルウェアを追加
//     ->name('login.store'); // 名前付きルートの定義

// Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
// Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');


// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
//     ->name('logout');

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

// その他ページのルート
Route::get('/sell', function () {
    return view('sell');
});

Route::get('/item', function () {
    return view('item');
});

Route::get('/purchase', function () {
    return view('purchase');
});

Route::get('/purchase/address', function () {
    return view('address');
});
