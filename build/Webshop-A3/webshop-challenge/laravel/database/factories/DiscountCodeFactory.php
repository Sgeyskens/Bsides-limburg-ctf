<?php

namespace Database\Factories;

use App\Models\DiscountCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiscountCode>
 */
class DiscountCodeFactory extends Factory
{
    protected $model = DiscountCode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('????##')),
            'discount_percentage' => 10,
            'discount_amount' => 0,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth(),
            'max_uses' => 100,
            'current_uses' => 0,
            'applies_to' => null,
            'minimum_purchase' => 0,
        ];
    }

    /**
     * Create a percentage discount code.
     */
    public function percentage(float $percent): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_percentage' => $percent,
            'discount_amount' => 0,
        ]);
    }

    /**
     * Create a fixed amount discount code.
     */
    public function fixedAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_percentage' => 0,
            'discount_amount' => $amount,
        ]);
    }

    /**
     * Set minimum purchase requirement.
     */
    public function minimumPurchase(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'minimum_purchase' => $amount,
        ]);
    }

    /**
     * Create an expired discount code.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_from' => now()->subMonth(),
            'valid_until' => now()->subDay(),
        ]);
    }

    /**
     * Create a discount code that has reached its usage limit.
     */
    public function exhausted(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_uses' => 10,
            'current_uses' => 10,
        ]);
    }

    /**
     * Create a not yet valid discount code.
     */
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_from' => now()->addDay(),
            'valid_until' => now()->addMonth(),
        ]);
    }
}
