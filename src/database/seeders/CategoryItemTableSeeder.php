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
        Item::find(2)->categories()->sync([2]);
        Item::find(3)->categories()->sync([9]);
        Item::find(4)->categories()->sync([1,5]);
        Item::find(5)->categories()->sync([2]);
        Item::find(6)->categories()->sync([2]);
        Item::find(7)->categories()->sync([1,4]);
        Item::find(8)->categories()->sync([10]);
        Item::find(9)->categories()->sync([2,10]);
        Item::find(10)->categories()->sync([1,6]);
    }
}
