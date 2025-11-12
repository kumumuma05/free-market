<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // メール未認証なら認証誘導画面へ
        if (! $user->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();

            return redirect('/email/verify');
        }

        // 認証済みならトップへ
        return redirect('/');
    }
}