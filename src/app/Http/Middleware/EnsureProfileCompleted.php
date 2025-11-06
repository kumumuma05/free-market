<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {

        $user = $request->user();

        // 未ログインユーザは対象外（authで制御）
        if (!$user) {
            return $next($request);
        }

        // メール未承認ユーザーはメール誘導画面へ
        if (!$user->hasVerifiedEmail()) {
            if(
                !$request->is('email/verify') &&
                !$request->is('email/verify/*') &&
                !$request->is('email/verification-notification') &&
                !$request->is('logout')
            ) {
                return redirect('/email/verify');
            }
        }

        // プロフィール未完了はプロフィール設定画面へ
        if (!$user->profile_completed && !$this->isWhitelisted($request)) {
            return redirect('/mypage/profile')->with('status', 'プロフィール登録を完了してください');
        }
        return $next($request);
    }


    private function isWhitelisted(Request $request) {
        return $request->is('mypage/profile')
            || $request->is('mypage/profile/*')
            || $request->is('logout')
            || $request->is('email/verify*');
    }
}
