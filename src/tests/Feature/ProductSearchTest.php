<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;

class ProductSearchTest extends TestCase
{

    /**
     * 商品名で部分一致検索ができる
     */
    public function test_product_name_partial_match_search(){

        // 準備
        Item::factory()->create(['product_name' => '腕時計']);
        Item::factory()->create(['product_name' => 'HDD']);
        Item::factory()->create(['product_name' => '玉ねぎ3束']);

        //商品一覧画面表示
        $this->get('/')->assertOk();

        // 検索欄にキーワード入力
        $response = $this->get('/item/search?keyword=腕')
            ->assertOk()
            ->assertSee('腕時計')
            ->assertDontSee('HDD')->assertDontSee('玉ねぎ3束');
    }
}
