<?php

namespace Database\Factories;

use App\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->text(),
            'url' => $this->faker->unique()->url(),
        ];
    }
}
