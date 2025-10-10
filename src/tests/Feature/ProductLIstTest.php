<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class ProductLIstTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_display_all_items()
    {
        $user = User::factory()->create();

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
}

