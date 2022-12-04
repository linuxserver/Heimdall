<?php

namespace Database\Factories;

use App\Item;
use App\ItemTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemTagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ItemTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [];
    }
}
