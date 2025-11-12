<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest as AppLoginRequest;
use App\Http\Responses\RegisterResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * アプリケーションサービスの登録
     *
     * @return void
     */
    public function register()
    {
        //
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
