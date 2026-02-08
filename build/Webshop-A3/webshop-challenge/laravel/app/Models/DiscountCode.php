<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;
    protected $table = 'discount_code';
    protected $primaryKey = 'code_id';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'code_id';
    }

    protected $fillable = [
        'code',
        'discount_percentage',
        'discount_amount',
        'valid_from',
        'valid_until',
        'max_uses',
        'current_uses',
        'applies_to',
        'minimum_purchase', // NEW: Minimum purchase amount required to use this code
    ];

    protected $casts = [
        'discount_percentage' => 'float',
        'discount_amount' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'max_uses' => 'integer',
        'current_uses' => 'integer',
    ];

    /**
     * Check if the discount code is valid
     */
    public function isValid(): bool
    {
        $today = now()->startOfDay();

        // Check date range
        if ($today->lt($this->valid_from) || $today->gt($this->valid_until)) {
            return false;
        }

        // Check usage limit
        if ($this->max_uses > 0 && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Check if the discount code can be applied to a given cart amount
     * NEW METHOD: Checks minimum purchase requirement
     */
    public function canApplyToAmount(float $amount): bool
    {
        return $amount >= $this->minimum_purchase;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     * Caps discount to ensure total remains at least $0.01 (prevents $0 total through normal means).
     * The race condition exploit can still bring total to $0 by stacking multiple discounts.
     */
    public function calculateDiscount(float $subtotal): float
    {
        // Ensure there's always at least 1 cent remaining after discount
        // Use round() to avoid floating point precision issues
        $maxDiscount = round(max($subtotal - 0.01, 0), 2);

        // If percentage discount
        if ($this->discount_percentage > 0) {
            $discount = round($subtotal * ($this->discount_percentage / 100), 2);
            return min($discount, $maxDiscount);
        }

        // If fixed amount discount
        if ($this->discount_amount > 0) {
            return min(round($this->discount_amount, 2), $maxDiscount);
        }

        return 0;
    }

    /**
     * Increment the usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('current_uses');
    }
}

