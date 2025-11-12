<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class SippingAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 認証済みユーザー作成
     */
    private function createUser()
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * 変更した配送先が商品購入画面に反映
     */
    public function test_changed_shipping_address_is_reflected()
    {
        // 準備
        $user = $this->createUser();
        $item = Item::factory()->create();

        // ログイン
        $this->actingAs($user);

        // 送付先住所変更画面で新しい住所を登録（POST）
        $response = $this->post("/purchase/address/{$item->id}", [
            'shipping_postal' => '222-2222',
            'shipping_address' => '北海道札幌市大通１丁目',
            'shipping_building' => 'テレビ塔MS1',
        ]);
        $response->assertRedirect("/purchase/{$item->id}");

        // 商品購入画面を開く
        $response = $this->get("/purchase/{$item->id}");

        // 新しく登録した住所が画面に反映されていることを確認
        $response->assertSee('222-2222');
        $response->assertSee('北海道札幌市大通１丁目');
        $response->assertSee('テレビ塔MS1');
    }

    /**
     * 購入商品に配送先住所が紐づいている
     */
    public function test_shipping_address_is_associated_with_purchase_item()
    {
        // 準備
        $user = $this->createUser();
        $item = Item::factory()->create();

        // ログイン
        $this->actingAs($user);

        // 送付先住所変更画面で新しい住所を登録（POST）
        $shippingData = [
            'shipping_postal' => '222-2222',
            'shipping_address' => '北海道札幌市大通１丁目',
            'shipping_building' => 'テレビ塔MS1',
        ];
        $response = $this->post("/purchase/address/{$item->id}", $shippingData);
        $response->assertRedirect("/purchase/{$item->id}");

        // 支払方法を選択後、小計にそれが反映される
        $response = $this->get("/purchase/{$item->id}?payment_method=1");
        $response->assertStatus(200);

        // 商品購入
        $response = $this->post("/purchase/{$item->id}", [
            'shipping_postal'   => $shippingData['shipping_postal'],
        'shipping_address'  => $shippingData['shipping_address'],
        'shipping_building' => $shippingData['shipping_building'],
        ]);
        $response->assertRedirect('/');

        // DBに変更後の送付先住所が登録されていることを確認
        $this->assertDatabaseHas('purchases', [
            'buyer_id'          => $user->id,
            'item_id'          => $item->id,
            'shipping_postal'  => $shippingData['shipping_postal'],
            'shipping_address' => $shippingData['shipping_address'],
            'shipping_building'=> $shippingData['shipping_building'],
            'payment_method'   => 1,
        ]);
        // 売却済みを確認
        $this->assertTrue($item->fresh()->is_sold == 1);
    }
}