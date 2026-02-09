<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Database\seeders\CategoryTableSeeder;

class RegisteringProductInformationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品画面で必要な情報が保存できること
     */
    public function test_sell_page_saves_item_with_required_fields()
    {
        // 準備
        $this->seed(CategoryTableSeeder::class);
        $categories = Category::take(2)->get();

        $user = User::factory()->create();

        Storage::fake('public');
        $tempPath = 'tmp/item_image/' . $user->id . '/current.jpg';
        Storage::disk('public')->put($tempPath, 'dummy');


        // ログイン
        $this->actingAs($user);

        // 出品するボタンを押下
        $response = $this
            ->withSession(['temp_image' => $tempPath])
            ->post('/sell', [
            'category_ids' => $categories->pluck('id')->toArray(),
            'condition'    => 2,
            'product_name' => 'テスト商品',
            'brand'        => 'テストブランド',
            'description'  => 'テスト商品の説明',
            'price'        => 5000,
        ]);

        // リダイレクト確認
        $response->assertRedirect('/mypage');
        $response->assertSessionHas('status', '商品を出品しました');

        // itemsテーブルに正しく保存されているか
        $this->assertDatabaseHas('items', [
            'user_id'      => $user->id,
            'product_name' => 'テスト商品',
            'brand'        => 'テストブランド',
            'description'  => 'テスト商品の説明',
            'price'        => 5000,
            'condition'    => 2,
        ]);

        // カテゴリの紐付け確認
        $item = Item::where('product_name', 'テスト商品')->firstOrFail();
        foreach ($categories as $category) {
            $this->assertDatabaseHas('category_items', [
                'item_id'     => $item->id,
                'category_id' => $category->id,
            ]);
        }

        // 一時ファイル削除
        $this->assertFalse(
            Storage::disk('public')->exists($tempPath));
    }
}
