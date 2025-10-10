<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductLIstTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_display_all_items()
    {
        $user = User::factory()->create();

        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');
        $response->assetStatus(200);

        foreach ($items as $atem) {
            $response->assertSee($item->product_neme);
        }

    }
}
