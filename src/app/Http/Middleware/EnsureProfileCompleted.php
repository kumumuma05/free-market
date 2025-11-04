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

        // メール未承認ユーザーは対象外（verifiedで制御）
        if (!$user->hasVerifiedEmail()) {
            return $next($request);
        }

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
