<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class ProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 認証済みユーザー作成
     */
    private function createBuyer()
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * 認証済み出品者作成
     */
    private function createSeller()
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * 出品者に紐づいた商品作成
     */
    private function createItem(User $seller): Item
    {
        return Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => 0,
        ]);
    }

    /**
     * 購入完了
     */
    public function test_purchase_complete()
    {
        // 準備
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $item = $this->createItem($seller);

        // ログイン
        $this->actingAs($buyer);

        // 購入画面表示
        $this->get("/purchase/{$item->id}")
            ->assertStatus(200)
            ->assertSee($item->product_name);

        // 購入するボタン押下
        $response = $this->post("/purchase/{$item->id}", [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 1,
            'shipping_postal' => $buyer->postal,
            'shipping_address' => $buyer->address,
            'shipping_building'=> $buyer->building,
        ]);

        $response->assertRedirect('/');

        // 購入情報登録確認
        $this->assertDatabaseHas('purchases', [
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 1,
        ]);

        $this->assertTrue($item->fresh()->is_sold == 1);
    }

    /**
     * 購入商品は商品一覧画面で「sold」と表示される
     */
    public function test_purchased_items_will_be_marked_as_sold() {
        // 準備
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $item = $this->createItem($seller);

        // ログイン
        $this->actingAs($buyer);

        // 購入画面表示
        $this->get("/purchase/{$item->id}")
            ->assertStatus(200)
            ->assertSee($item->product_name);

        // 購入するボタン押下
        $response = $this->post("/purchase/{$item->id}", [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 1,
            'shipping_postal' => $buyer->postal,
            'shipping_address' => $buyer->address,
            'shipping_building'=> $buyer->building,
        ]);

        $response->assertRedirect('/');

        // 商品一覧画面へ
        $response = $this->followRedirects($response);

        // 購入した商品にSoldが表示されている
        $response->assertSeeInOrder([
            'Sold',
            $item->product_name,
        ]);
    }

    /**
     * プロフィールに購入した商品が追加されている
     */
    public function test_purchase_items_will_be_listed_in_profile()
    {
        // 準備
        $buyer = $this->createBuyer();
        $seller = $this->createSeller();
        $item = $this->createItem($seller);

        // ログイン
        $this->actingAs($buyer);

        // 購入画面を開く
        $this->get("/purchase/{$item->id}")
            ->assertStatus(200);

        // 購入するボタン押下
        $response = $this->post("/purchase/{$item->id}", [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'payment_method' => 1,
            'shipping_postal' => $buyer->postal,
            'shipping_address' => $buyer->address,
            'shipping_building'=> $buyer->building,
        ]);

        $response->assertRedirect('/');

        $this->assertSame(1, $item->fresh()->is_sold);

        $this->assertDatabaseHas('purchases', [
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 1,
        ]);

        // プロフィール画面表示(購入した商品一覧)
        $profile = $this->get('mypage?page=buy');
        $profile->assertStatus(200);

        // 購入した商品が表示されている
        $profile->assertSee($item->product_name);
    }
}
