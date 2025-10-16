<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeFunctionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * いいねを登録、合計数が増加する
     */
    public function test_like_registration_and_total_number_increase(){

        // ユーザー作成
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
        $seller = User::factory()->create();

        // アイテム作成
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // ログイン
        $this->followingRedirects()->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password'
        ])->assertOk();

        // 商品詳細画面へ移動
        $this->get("/item/{$item->id}")->assertOk();

        //いいね件数の確認
        $this->assertSame(0, $item->likedUsers()->count());

        // いいね実行
        $response = $this->from("/item/{$item->id}")->followingRedirects()->post("/item/{$item->id}/like");

        // データベースへの登録確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が１件に増えていること
        $this->assertEquals(1, $item->fresh()->likedUsers()->count());
    }


}
