<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Purchase::create([
            'item_id' => 1,
            'buyer_id' => 2,
        ]);
        Purchase::create([
            'item_id' => 2,
            'buyer_id' => 1,
        ]);
        Purchase::create([
            'item_id' => 3,
            'buyer_id' => 1,
        ]);
        Purchase::create([
            'item_id' => 4,
            'buyer_id' => 2,
        ]);
        Purchase::create([
            'item_id' => 5,
            'buyer_id' => 3,
        ]);
        Purchase::create([
            'item_id' => 6,
            'buyer_id' => 2,
        ]);
        Purchase::create([
            'item_id' => 7,
            'buyer_id' => 2,
        ]);
        Purchase::create([
            'item_id' => 8,
            'buyer_id' => 3,
        ]);
        Purchase::create([
            'item_id' => 9,
            'buyer_id' => 1,
        ]);
    }
}
