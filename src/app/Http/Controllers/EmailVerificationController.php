<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{

    /**
     * メール認証誘導画面表示
     */
    public function showNotice() {
        return view('auth.verify-email');
    }

    // public function showGuide() {
    //     return view ('auth.email-verification');
    // }

    /**
     * メール認証完了後、プロフィール編集画面へ遷移（プロフィール編集済なら商品一覧画面へ遷移）
     */
    public function verify(EmailVerificationRequest $request) {

        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            $request->fulfill();
        }

        $complete = filled($user->profile_image)
            && filled($user->name)
            && filled($user->postal)
            && filled($user->address);

        return $complete
        ? redirect('/')
        : redirect('/mypage/profile');
    }

    /**
     * 認証メールの再送信
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            // すでに認証済みならプロフィール画面へ遷移
            return redirect('/mypage/profile');
        }

        // メールを再送信
        $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送信しました。');
    }
}
