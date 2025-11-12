<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class SelectPaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 小計画面で変更が反映される
     */
    public function test_changes_are_reflected_in_the_subpalns()
    {
        // 準備
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item = Item::factory()->create();

        // ログイン
        $this->actingAs($buyer);

        // 購入画面を開く
        $response = $this->get("/purchase/{$item->id}")
            ->assertStatus(200)
            ->assertSee('選択してください');

        // 支払方法を選択後、小計にそれが反映される
        $response = $this->get("/purchase/{$item->id}?payment_method=1");
        $response->assertStatus(200)->assertSee('コンビニ払い');
    }
}
