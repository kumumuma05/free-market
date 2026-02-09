<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    /**
     * メール認証誘導画面表示
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * メール認証完了後、プロフィール編集画面へ遷移（プロフィール編集済なら商品一覧画面へ遷移）
     */
    public function verify(EmailVerificationRequest $request) {

        $request->fulfill();

        $user = $request->user();

        return $user->profile_completed
            ? redirect('/')
            : redirect('/mypage/profile');
    }

    /**
     * 認証メールの再送信
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            // すでに認証済みならプロフィール画面へ遷移
            return redirect('/mypage/profile');
        }

        // メールを再送信
        $user->sendEmailVerificationNotification();

        return back()->with('status', '認証メールを再送信しました。');
    }
}
