<?php

namespace Database\Seeders;

use App\Models\DiscountCode;
use Illuminate\Database\Seeder;

class VulnerableDiscountCodeSeeder extends Seeder
{
    /**
     * Seed discount codes that are vulnerable to race condition exploitation.
     * 
     * These discount codes have minimum purchase requirements and can be
     * applied multiple times through concurrent requests.
     */
    public function run(): void
    {
        // High-value percentage discount - perfect for exploitation
        DiscountCode::updateOrCreate(
            ['code' => 'RACE50'],
            [
            'discount_percentage' => 50, // 50% off
            'discount_amount' => 0,
            'valid_from' => now()->subDays(7),
            'valid_until' => now()->addDays(30),
            'max_uses' => 1000,
            'current_uses' => 0,
            'minimum_purchase' => 50.00, // Requires $50 minimum
            'applies_to' => 'all',
        ]);

        // Fixed amount discount
        DiscountCode::updateOrCreate(
            ['code' => 'EXPLOIT20'],
            [
                'discount_percentage' => 0,
                'discount_amount' => 20.00, // $20 off
                'valid_from' => now()->subDays(7),
                'valid_until' => now()->addDays(30),
                'max_uses' => 500,
                'current_uses' => 0,
                'minimum_purchase' => 30.00, // Requires $30 minimum
                'applies_to' => 'all',
            ]
        );

        // Extreme discount for testing
        DiscountCode::updateOrCreate(
            ['code' => 'FREEBIE100'],
            [
                'discount_percentage' => 100, // 100% off (free!)
                'discount_amount' => 0,
                'valid_from' => now()->subDays(1),
                'valid_until' => now()->addDays(7),
                'max_uses' => 100,
                'current_uses' => 0,
                'minimum_purchase' => 100.00, // Requires $100 minimum
                'applies_to' => 'all',
            ]
        );

        // Smaller discount for games
        DiscountCode::updateOrCreate(
            ['code' => 'GAME15'],
            [
                'discount_percentage' => 15, // 15% off
                'discount_amount' => 0,
                'valid_from' => now()->subDays(14),
                'valid_until' => now()->addDays(60),
                'max_uses' => 2000,
                'current_uses' => 0,
                'minimum_purchase' => 25.00, // Requires $25 minimum
                'applies_to' => 'games',
            ]
        );

        // Fixed $10 off for merch
        DiscountCode::updateOrCreate(
            ['code' => 'MERCH10'],
            [
                'discount_percentage' => 0,
                'discount_amount' => 10.00, // $10 off
                'valid_from' => now()->subDays(3),
                'valid_until' => now()->addDays(14),
                'max_uses' => 300,
                'current_uses' => 0,
                'minimum_purchase' => 20.00, // Requires $20 minimum
                'applies_to' => 'merch',
            ]
        );

        // Ultimate exploit code - high percentage + low minimum
        DiscountCode::updateOrCreate(
            ['code' => 'ULTIMATE75'],
            [
                'discount_percentage' => 75, // 75% off!
                'discount_amount' => 0,
                'valid_from' => now(),
                'valid_until' => now()->addDays(365),
                'max_uses' => 5000,
                'current_uses' => 0,
                'minimum_purchase' => 10.00, // Only $10 minimum - easy to exploit
                'applies_to' => 'all',
            ]
        );

        echo "Seeded 6 vulnerable discount codes for race condition testing.\n";
        echo "Try these codes with Burp Suite Intruder:\n";
        echo "  - RACE50 (50% off, $50 min)\n";
        echo "  - EXPLOIT20 ($20 off, $30 min)\n";
        echo "  - FREEBIE100 (100% off, $100 min)\n";
        echo "  - GAME15 (15% off games, $25 min)\n";
        echo "  - MERCH10 ($10 off merch, $20 min)\n";
        echo "  - ULTIMATE75 (75% off, $10 min) ⚠️ MOST VULNERABLE\n";
    }
}