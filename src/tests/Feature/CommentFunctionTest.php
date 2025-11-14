<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentFunctionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みのユーザはコメントを送信できる
     */
    public function test_authenticated_use_can_post_comment_and_comment_count_increases()
    {

        // ユーザー作成
        $this->seed();

        $user = User::firstOrFail();
        $item = Item::firstOrFail();
        $user->forceFill(['email_verified_at' => now()])->save();

        // 出品者・商品作成
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $seller->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // 投稿前のコメント件数
        $beforeCount = $item->comments()->count();

        // コメント送信
        $response = $this
            ->from("/item/{$item->id}")
            ->post("/item/{$item->id}/comments", [
                'body' => 'テストコメント'
        ]);

        // 戻り先確認
        $response->assertRedirect("/item/{$item->id}");

        // DBに登録されていることを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'テストコメント',
        ]);

        // 件数が一件増えていることを確認
        $item->refresh();
        $this->assertEquals($beforeCount +1, $item->comments()->count());
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     */
    public function test_guest_user_cannot_post_comment()
    {
        // 準備
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        // コメント送信前の件数
        $beforeCount = comment::count();

        // ゲスト状態でコメント送信
        $response = $this->from("/item/{$item->id}")->post("/item/{$item->id}/comments", ['body' => 'テスト']);

        // ログイン画面にリダイレクトされる
        $response->assertRedirect('/login');

        // コメントが増えていない
        $this->assertEquals($beforeCount, Comment::count());
    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_comment_validation_error_is_displayed_when_comment_is_empty()
    {
        // 準備
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        // コメントを空欄で送信
        $response = $this
            ->from("/item/{$item->id}")
            ->post("/item/{$item->id}/comments", [
                'body' => '',
            ]);

        // 元のページにリダイレクトされる
        $response->assertRedirect("/item/{$item->id}");

        // セッションにバリデーションエラーが入っている
        $response->assertSessionHasErrors(['body']);

        // エラーメッセージを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('コメントを入力してください');
    }

    /**
     * コメントが255文字以上の場合、バリデーションメッセージが表示される
     */
    public function test_comment_validation_error_is_displayed_when_comment_is_too_long()
    {
        // 準備
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'user_id' => $user->id,
        ]);

        // 255文字を超える文字列を生成
        $longComment = str_repeat('あ', 260);

        // コメント送信
        $response = $this
            ->from("/item/{$item->id}")
            ->post("/item/{$item->id}/comments", [
                'body' => $longComment,
            ]);

        // 元のページにリダイレクト
        $response->assertRedirect("/item/{$item->id}");

        // セッションにバリデーションエラーが入っている
        $response->assertSessionHasErrors(['body']);

        // エラーメッセージを確認
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('コメントは255文字以内で入力してください');
    }
}