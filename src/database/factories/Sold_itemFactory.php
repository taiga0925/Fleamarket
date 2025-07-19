<?php

namespace Database\Factories;

use App\Models\Sold_item;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

    class Sold_itemFactory extends Factory
    {
        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = Sold_item::class;

        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array
        {
            return [
                'user_id' => User::factory(),
                'item_id' => Item::factory(),
                'method' => $this->faker->randomElement(['コンビニ支払い', 'クレジットカード支払い']),
                'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            ];
        }
        
    }
