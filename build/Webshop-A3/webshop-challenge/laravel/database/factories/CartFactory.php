<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'created_date' => now(),
            'last_updated' => now(),
            'discount_code' => null,
            'discount_amount' => 0,
        ];
    }

    /**
     * Apply a discount to the cart.
     */
    public function withDiscount(string $code, float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_code' => $code,
            'discount_amount' => $amount,
        ]);
    }
}
