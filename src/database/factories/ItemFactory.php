<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'image' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'item' => $this->faker->word(),
            'brand' => $this->faker->optional()->company(),
            'detail' => $this->faker->paragraph(),
            'money' => $this->faker->numberBetween(100, 50000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
