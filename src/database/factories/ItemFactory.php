<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'product_name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->numberBetween(500, 5000),
            'condition' => $this->faker->numberBetween(1, 4),
            'image_path' => null,
            'is_sold' => $this->faker->boolean(20),
        ];
    }
}
