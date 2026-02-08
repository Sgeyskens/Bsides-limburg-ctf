<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 100),
            'product_type' => fake()->randomElement(['movie', 'game', 'merch']),
            'image_url' => null,
        ];
    }

    /**
     * Indicate that the product is a movie.
     */
    public function movie(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'movie',
            'name' => 'Friday the 13th ' . fake()->randomElement(['Part I', 'Part II', 'Part III', 'Part IV']),
        ]);
    }

    /**
     * Indicate that the product is a game.
     */
    public function game(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'game',
            'name' => 'Friday the 13th: ' . fake()->words(2, true),
        ]);
    }

    /**
     * Indicate that the product is merch.
     */
    public function merch(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'merch',
            'name' => 'Jason ' . fake()->randomElement(['T-Shirt', 'Mask', 'Poster', 'Mug']),
        ]);
    }

    /**
     * Indicate that the product is a T-Shirt (requires size).
     */
    public function tshirt(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'merch',
            'name' => 'Jason Voorhees T-Shirt',
        ]);
    }
}
