<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class CategoryItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::find(1)->categories()->sync([1,5]);
    }
}
