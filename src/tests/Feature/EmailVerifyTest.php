<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;


class EmailVerifyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録後、認証メールが送信される
     */
    public function test_verification_email_is_sent_after_registration()
    {
        // 認証メール送信をフェイク
        Notification::fake();

        // 会員登録実行
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // 認証案内画面へリダイレクト
        $response->assertRedirect('/email/verify');

        // ユーザーが作成されていること
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        // 作成されたユーザーに認証メールが送付されている
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    /**
     * メール認証誘導画面で「認証はこちらから」ボタンが MailHog を指している
     */
    public function test_verify_notice_page_has_link_to_mailhog()
    {
        // ユーザーを作成
        $user = User::factory()->unverified()->create();

        // メール認証誘導画面を表示
        $response = $this
            ->actingAs($user)
            ->get('/email/verify');
        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');

        // MailHog へのリンクが正しく埋め込まれているか
        $response->assertSee(
            '<a class="verify-email__link-button" href="http://localhost:8025">認証はこちらから</a>', false
        );
    }

    /**
     * メール認証を完了するとプロフィール設定画面に遷移する
     */
    public function test_user_is_redirected_to_profile_after_email_verification()
    {
        // ユーザー作成
        $user = User::factory()->unverified()->create([
            'profile_completed' => false,
        ]);

        // 署名付きURL（認証リンク）を生成
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id'   => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // 認証リンクにアクセス
        $response = $this->actingAs($user)->get($verifyUrl);

        // プロフィール設定画面にリダイレクトされる
        $response->assertRedirect('/mypage/profile');

        // メール認証が完了済みになっていることを確認
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
