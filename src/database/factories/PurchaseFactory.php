<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
use App\Models\User;
use App\models\Item;

class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = FakerFactory::create('ja_JP');

        return [
            'item_id' => Item::factory(),
            'buyer_id' => User::factory(),
            'payment_method' => $this->faker->numberBetween(1, 2),
            'shipping_postal' => '123-4567',
            'shipping_address' => $faker->address(),
            'shipping_building' => $faker->boolean(30) ? $faker->secondaryAddress() : null,
        ];
    }
}
