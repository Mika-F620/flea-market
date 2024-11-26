<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ユーザー登録処理
        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録ビュー
        Fortify::registerView(fn() => view('auth.register'));

        // 登録後のリダイレクト設定
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/mypage/profile');
            }
        });

        // ログインビュー
        Fortify::loginView(fn() => view('auth.login'));

        // カスタム認証ロジック
        Fortify::authenticateUsing(function (Request $request) {
            // 入力値を取得
            $credentials = $request->only(['username_email', 'password']);
        
            // バリデーションの実施
            Validator::make($credentials, [
                'username_email' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8'],
            ], [
                'username_email.required' => 'ユーザー名またはメールアドレスを入力してください。',
                'password.required' => 'パスワードを入力してください。',
                'password.min' => 'パスワードは8文字以上で入力してください。',
            ])->validate();
        
            // 入力がメールアドレス形式かユーザー名かを判定
            $loginField = filter_var($credentials['username_email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
            // ユーザーを検索
            $user = User::where($loginField, $credentials['username_email'])->first();
        
            // パスワードが一致する場合は認証成功
            if ($user && Hash::check($credentials['password'], $user->password)) {
                return $user;
            }
        
            return null; // 認証失敗
        });

        // ログインのレートリミット
        RateLimiter::for('login', function (Request $request) {
            $usernameEmail = (string) $request->username_email;
            return Limit::perMinute(10)->by($usernameEmail . $request->ip());
        });

        // ログイン後のリダイレクト先
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect('/?page=mylist'); // ログイン後のリダイレクト先
            }
        });
    }
}
