<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class MyListPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * マイリストでいいねした商品だけが表示される
     */
    public function test_only_liked_product_are_displayed()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $likedItem = Item::factory()->create([
            'product_name' => 'like',
            'image_path' => 'item_image/like.png'
        ]);

        $otherItem = Item::factory()->create([
            'product_name' => 'other',
            'image_path' => 'item_image/other.png'
        ]);

        Like::create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        $this->followingRedirects()->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password'
        ])->assertOk();

        $page = $this->get('/?tab=mylist')->assertOk();

        $page->assertSeeText('like');
        $page->assertSee('like.png');

        $page->assertDontSeeText('other');
        $page->assertDontSee('other.png');
    }
}
