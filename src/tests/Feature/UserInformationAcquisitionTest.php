<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class UserInformationAcquisitionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト用ユーザー作成
     */
    private function createUser()
    {
        return User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profile/test.jpg',
        ]);
    }

    /**
     * 必要な情報が取得できる
     */
    public function test_mypage_displays_profiles_and_item_lists()
    {
        // 準備
        $user = $this->createUser();
        $seller = User::factory()->create();

        $sellItem = Item::factory()->create([
            'user_id' =>$user->id,
            'product_name' => '出品した商品'
        ]);
        $purchasedItem = Item::factory()->create([
            'user_id' => $seller->id,
            'product_name' => '購入した商品',
        ]);

        // 購入履歴作成
        Purchase::factory()->create([
            'buyer_id' => $user->id,
            'item_id' => $purchasedItem->id,            'payment_method' => 1,
        ]);

        // ログイン
        $this->actingAs($user);

        // マイページ（購入タブ）
        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);

        // ユーザー名・画像・購入商品が表示されている
        $response->assertSee($user->name);
        $response->assertSee($user->profile_image_url, false);
        $response->assertSee('購入した商品');

        // マイページ（出品タブ）
        $response = $this->get('/mypage?page=sell');
        $response->assertStatus(200);

        // ユーザー名・画像・出品商品が表示されている
        $response->assertSee($user->name);
        $response->assertSee($user->profile_image_url, false);
        $response->assertSee('出品した商品');
    }
}

