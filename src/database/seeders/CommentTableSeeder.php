<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Comment::updateOrCreate(
            ['id' =>1],
            ['item_id' => 1, 'user_id' => 1, 'body' => 'コメントです']
        );
    }
}
