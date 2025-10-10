<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout (){
        // ログイン
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
        $response->assertRedirect('/');

        $response = $this->get('/');
        $response->assertStatus(200);

        // ログアウト
        $response = $this->post('/logout');
        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
