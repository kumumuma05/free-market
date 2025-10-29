<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // メール認証していない場合は認証へ
        if (! $request->user()->hasVerifiedEmail()) {
            return redirect('/email/verify');
        }
        // メール認証後はプロフィール設定へ
    return redirect('mypage/profile');
    }
}
