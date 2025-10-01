<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest as AppLoginRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションサービスの登録
     *
     * @return void
     */
    public function register()
    {
        // fortifyがユーザー登録後に呼び出すレスポンスクラスをデフォルトからRegisterResponseに差し替える
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);

        // fortifyがログイン処理で使用するデフォルトのLoginRequestを差し替える
        $this->app->bind(FortifyLoginRequest::class,AppLoginRequest::class);
    }

    /**
     * アプリケーション起動後の設定
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
