<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // 登録直後だけプロフィール画面へ
    return redirect()->route('mypage.profile')->with('first_login', true);
    }
}
