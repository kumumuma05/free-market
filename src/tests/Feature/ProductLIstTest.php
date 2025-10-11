<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ProductLIstTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 全商品を一覧表示する
     */
    public function test_product_page_display_all_items()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee($items[0]->product_name);
        $response->assertSee($items[0]->image_path);

        $response->assertSee($items[1]->product_name);
        $response->assertSee($items[1]->image_path);

        $response->assertSee($items[2]->product_name);
        $response->assertSee($items[2]->image_path);
    }

    /**
     * 購入済み商品に「sold」ラベルを表示
     */
    public function test_sold_display_check() {
        Item::factory()->create([
            'product_name' => 'aaa',
            'image_path' => 'item_image/testa.image.png',
            'is_sold' => true
        ]);

        $this->get('/')->assertOk()
            ->assertSee('aaa')
            ->assertSee('testa.image.png')
            ->assertSeeText('Sold');
    }

    /**
     * 自分が出品した商品は表示されない
     */
    public function test_login_user_product_not_displayed()
    {
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);

        $myItem = Item::factory()->create([
            'user_id' => $user1->id,
            'product_name' => 'My Item by User1',
            'image_path' => 'item_image/my.png',
        ]);

        $otherItem = Item::factory()->create([
            'user_id' => $user2->id,
            'product_name' => 'Others Item by User2',
            'image_path' => 'item_image/other.png',
        ]);

        $this->get('/login')->assertOk()->assertViewIs('auth.login');

        $response = $this->post('/login', [
            'email' => 'user1@example.com',
            'password' => 'password',
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user1);

        $page = $this->get('/')->assertOk();
        $page->assertDontSee($myItem->product_name);
        $page->assertSee($otherItem->product_name);
    }
}

