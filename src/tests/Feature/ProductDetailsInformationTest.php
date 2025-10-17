<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Like;

class ProductDetailsInformationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品詳細一覧画面の表示チェック
     */
    public function test_get_product_details_information(){

        // 商品
        $item = Item::factory()->create([
            'product_name' => 'product',
            'brand' => 'brand',
            'description' => 'description',
            'price' => 10000,
            'condition' => 1,
            'image_path' => 'item_image/test.png',
            'is_sold' => 1,
        ]);

        // ユーザー
        $user1 = User::factory()->create([
            'name' =>'aaa',
            'profile_image' => 'profile_image/aaa.png',
        ]);
        $user2 = User::factory()->create([
            'name' =>'bbb',
            'profile_image' => 'profile_image/bbb.png',
        ]);

        // いいね
        $like1 = Like::create([
            'user_id' => $user1->id,
            'item_id' => $item->id,
        ]);
        $like2 = Like::create([
            'user_id' => $user2->id,
            'item_id' => $item->id,
        ]);

        // コメント
        $comment1 = Comment::create([
            'item_id' => $item->id,
            'user_id' => $user1->id,
            'body' => 'user1_comment',
        ]);
        $comment2 = Comment::create([
            'item_id' => $item->id,
            'user_id' => $user2->id,
            'body' => 'user2_comment',
        ]);

        // カテゴリー
        $category1 = Category::create([
            'name' => 'category1'
        ]);
        $category2 = Category::create([
            'name' => 'category2'
        ]);
        $item->categories()->attach([$category1->id, $category2->id]);

        // 商品詳細画面表示
        $response = $this->get("/item/{$item->id}")->assertOk();
        $html = $response->getContent();

        $response->assertSeeText('product');
        $response->assertSeeText('brand');
        $response->assertSee('item_image/test.png');
        $response->assertSee(e(number_format(10000)));

        $response->assertSee('data-testid="like-icon"', false);
        $response->assertSee('data-testid="comment-icon"', false);
        $response->assertSee([
            (string)$item->likes_count,
            (string)$item->comment_count
        ]);
        $response->assertSeeText('description');
        $response->assertSeeText('category1');
        $response->assertSeeText('category2');
        $response->assertSeeText('良好');
        $this->assertMatchesRegularExpression('/コメント\(\s*2\s*\)/u', $html);
        $response->assertSeeText('aaa');
        $response->assertSee('profile_image/aaa.png');
        $response->assertSee('user1_comment');
    }

    /**
     * カテゴリの複数選択
     */
    public function test_category_multiple_selection()
    {

        // 商品
        $item = Item::factory()->create();

        // カテゴリー
        $category1 = Category::create([
            'name' => 'category1'
        ]);
        $category2 = Category::create([
            'name' => 'category2'
        ]);
        $item->categories()->attach([$category1->id, $category2->id]);

        // 商品詳細画面表示
        $response = $this->get("/item/{$item->id}")->assertOk();
        $response->assertSeeText('category1');
        $response->assertSeeText('category2');
    }
}
