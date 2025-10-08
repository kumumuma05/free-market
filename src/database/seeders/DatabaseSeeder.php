<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            CategoryTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemTableSeeder::class,
            CommentTableSeeder::class,
            PurchasesTableSeeder::class,
        ]);
    }
}
