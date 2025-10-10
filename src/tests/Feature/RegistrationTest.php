<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前入力必須
     */
    public function test_name_required()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('register')->post('/register', [
            'name' => '',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSee('お名前を入力してください');
    }

    /**
     * メールアドレス入力必須
     */
    public function test_mail_required()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('register')->post('/register', [
            'name' => '山田　太郎',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSee('メールアドレスを入力してください');
    }

    /**
     * メールアドレス形式チェック
     */
    public function test_email_format()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('register')->post('/register', [
            'name' => '山田　太郎',
            'email' => 'mail',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSee('メールアドレスはメール形式で入力してください');
    }

    /**
     * パスワード入力必須
     */
    public function test_password_required()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('register')->post('/register', [
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => '',
            'password_confirmation' => 'password',
        ]);

        $response->assertSee('パスワードを入力してください');
    }

    /**
     * パスワード文字制限
     */
    public function test_password_max()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('register')->post('/register', [
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => 'passwor',
            'password_confirmation' => 'password',
        ]);

        $response->assertSee('パスワードは8文字以上で入力してください');
    }

    /**
     * パスワード不一致
     */
    public function test_password_confirmed()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('register')->post('/register', [
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'passwordd',
        ]);

        $response->assertSee('パスワードと一致しません');
    }

    /**
     * 会員情報登録後、プロフィール画面に遷移
     */
    public function test_registration_success_and_redirect_profile_setup()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->from('register')->post('/register', [
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertRedirect('/mypage/profile');

        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);
    }
}
