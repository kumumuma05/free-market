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

        // 認証済みユーザー作成
        $this->seed();
        $user = User::firstOrFail();
        $user->forceFill(['email_verified_at' => now()])->save();

        // 出品者・商品作成
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // 商品詳細画面へ移動
        $this->get("/item/{$item->id}")->assertOk();

        //いいね件数の確認
        $this->assertEquals(0, $item->likedUsers()->count());

        // いいね実行
        $response = $this->from("/item/{$item->id}")->followingRedirects()->post("/item/{$item->id}/like");

        // データベースへの登録確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が１件に増えていることを確認
        $this->assertEquals(1, $item->fresh()->likedUsers()->count());
    }

    /**
     * いいね追加済みアイコンは色が変化する
     */
    public function test_the_color_of_the_like_icon_changes(){

        // ユーザー作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // 出品者・アイテム作成
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // 商品詳細画面へ移動(いいねされていない状態)
        $response = $this->get("/item/{$item->id}")->assertOk();
        $response->assertDontSee('is-active');

        // いいね送信
        $this->post("/item/{$item->id}/like")->assertRedirect();

        // データベースへの登録確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 商品詳細画面へ移動(is-activeクラスが追加されている（いいねされている状態)）
        $response = $this->get("/item/{$item->id}")->assertOk();
        $response->assertSee('is-active', false);
    }

    /**
     * いいね解除ができ、いいね数が減少する
     */
    public function test_unlike_removes_like_and_decreases_total_count()
    {
        // ユーザ作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // 出品者・商品作成
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // 1回目のいいね（登録）
        $this->post("/item/{$item->id}/like")->assertRedirect();
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertSame(1, $item->fresh()->likedUsers()->count());

        // 2回目のいいね（解除）
        $this->post("/item/{$item->id}/like")->assertRedirect();

        // likes テーブルから削除されていることを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 合計値が 0 に戻っていること
        $this->assertSame(0, $item->fresh()->likedUsers()->count());
    }
}
