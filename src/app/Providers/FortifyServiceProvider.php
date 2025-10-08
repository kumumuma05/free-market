<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Http\Responses\RegisterResponse as AppRegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest as AppLoginRequest;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 新規登録時のレスポンス変更
        $this->app->singleton(RegisterResponseContract::class, AppRegisterResponse::class);

        // ログイン画面でformrequestを使用
        $this->app->bind(FortifyLoginRequest::class, AppLoginRequest::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ログインユーザーの新規登録
        Fortify::createUsersUsing(CreateNewUser::class);

        // 会員登録画面の表示
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面の表示
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン回数制限（１分毎10回まで）
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });

        // 認証処理,バリデーションは独自のLoginRequestを使用,認証失敗時のエラーメッセージを日本語に変更
        // Fortify::authenticateUsing(function (Request $request){
        //     $form = app(LoginRequest::class);
        //     $form->setContainer(app())->setRedirector(app('redirect'));
        //     $form->merge($request->all());
        //     $form->validateResolved();

        //     $user = User::where('email', $request->email)->first();
        //     if($user && Hash::check($request->password, $user->password)) {
        //         return $user;
        //     }

            // throw ValidationException::withMessages([
            //     'email' => ['ログイン情報が登録されていません'],
            // ]);
    }
}
