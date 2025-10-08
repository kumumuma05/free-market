<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * メールアドレス入力必須
     */
    public function test_email_required()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSee('メールアドレスを入力してください');
    }

    /**
     * メールアドレス形式チェック
     */
    public function test_email_format()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('/login')->post('/login', [
            'email' => 'mail',
            'password' => 'password',
        ]);

        $response->assertSee('メールアドレスはメール形式で入力してください');
    }

    /**
     * パスワード入力必須
     */
    public function test_password_required()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('/login')->post('/login', [
            'email' => 'user@example.com',
            'password' => '',
        ]);

        $response->assertSee('パスワードを入力してください');
    }

    /**
     * ユーザー情報なし
     */
    public function test_no_registration_info()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->from('/login')->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ]);

        $response->assertSee('ログイン情報が登録されていません');
    }

    /**
     * ログイン処理の実行
     */
    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->from('/login')->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
    }
}