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
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Events\Registered;
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
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(fn() => view('auth.register'));

        // 登録後のリダイレクトを設定
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/mypage/profile');
            }
        });

        Fortify::loginView(fn() => view('auth.login'));

        Fortify::authenticateUsing(function (Request $request) {  // Laravel\Fortify\Http\Requests\LoginRequestを使用
            // バリデーションルールとメッセージを指定してValidatorを適用
            $validator = Validator::make(
                $request->only(['email', 'password']),
                [
                    'email' => ['required', 'string', 'email', 'max:255'],
                    'password' => ['required', 'string', 'min:8'],
                ],
                [
                    'email.required' => 'メールアドレスを入力してください',
                    'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
                    'email.max' => 'メールアドレスを255文字以下で入力してください',
                    'password.required' => 'パスワードを入力してください',
                    'password.min' => 'パスワードは8文字以上で入力してください',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // ユーザーが存在し、パスワードが一致するか確認
            $user = User::where('email', $request->email)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            // 入力値を取得
            $credentials = $request->only('email', 'password');

            // ユーザー名またはメールアドレスで検索
            $user = User::where('email', $credentials['email'])
                ->orWhere('name', $credentials['email']) // ユーザー名で検索
                ->first();

            // パスワードが一致する場合、認証成功
            if ($user && Hash::check($credentials['password'], $user->password)) {
                return $user;
            }

            return null;
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect('/?page=mylist'); // ログイン後のリダイレクト先
            }
        });
    }
}
