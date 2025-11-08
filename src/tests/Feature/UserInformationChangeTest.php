<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserInformationChangeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 初期値付きユーザー作成
     */
    private function createUser()
    {
        return User::factory()->create([
            'name'          => 'テストユーザー',
            'postal'        => '123-4567',
            'address'       => '北海道札幌市大通１丁目',
            'profile_image' => 'profile/test.jpg',
        ]);
    }

    /**
     * プロフィールページで各項目の初期値が表示されている
     */
    public function test_profile_page_displays_initial_values()
    {
        // 準備
        $user = $this->createUser();

        // ログイン
        $this->actingAs($user);

        // プロフィールページ表示
        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);

        // プロフィール画像が表示されているか
        $response->assertSee($user->profile_image_url);

        // ユーザー名の初期値が表示されているか
        $response->assertSee('テストユーザー');

        // 郵便番号の初期値が表示されているか
        $response->assertSee('123-4567');

        // 住所の初期値が表示されているか
        $response->assertSee('北海道札幌市大通１丁目');
    }
}
