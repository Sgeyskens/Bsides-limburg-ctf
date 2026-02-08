<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiscountCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discountCodes = [
            [
                'code' => 'FRIDAY13',
                'discount_percentage' => '13',
                'discount_amount' => 0.00,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'max_uses' => 100,
                'current_uses' => 0,
                'applies_to' => 'all',
                'minimum_purchase' => 0.00,
            ],
            [
                'code' => 'JASON2025',
                'discount_percentage' => '20',
                'discount_amount' => 0.00,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'max_uses' => 50,
                'current_uses' => 0,
                'applies_to' => 'all',
                'minimum_purchase' => 0.00,
            ],
            [
                'code' => 'CRYSTALLAKE',
                'discount_percentage' => '10',
                'discount_amount' => 0.00,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
                'max_uses' => 500,
                'current_uses' => 0,
                'applies_to' => 'merch',
                'minimum_purchase' => 0.00,
            ],
            [
                'code' => 'WELCOME15',
                'discount_percentage' => '15',
                'discount_amount' => 0.00,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(12),
                'max_uses' => 1000,
                'current_uses' => 0,
                'applies_to' => 'all',
                'minimum_purchase' => 0.00,
            ],
            // Vulnerable discount codes for CTF race condition challenge
            [
                'code' => 'ULTIMATE75',
                'discount_percentage' => '75',
                'discount_amount' => 0.00,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
                'max_uses' => 5000,
                'current_uses' => 0,
                'applies_to' => 'all',
                'minimum_purchase' => 10.00,
            ],
            [
                'code' => 'RACE50',
                'discount_percentage' => '50',
                'discount_amount' => 0.00,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'max_uses' => 1000,
                'current_uses' => 0,
                'applies_to' => 'all',
                'minimum_purchase' => 50.00,
            ],
        ];

     foreach ($discountCodes as $code) {
    DB::table('discount_code')->updateOrInsert(
        ['code' => $code['code']], // UNIQUE KEY
        [
            'discount_percentage' => $code['discount_percentage'],
            'discount_amount' => $code['discount_amount'],
            'valid_from' => $code['valid_from'],
            'valid_until' => $code['valid_until'],
            'max_uses' => $code['max_uses'],
            'current_uses' => $code['current_uses'],
            'applies_to' => $code['applies_to'],
            'minimum_purchase' => $code['minimum_purchase'] ?? 0.00,
            'updated_at' => now(),
            'created_at' => now(),
        ]
    );
}

        $this->command->info('Discount codes seeded successfully!');
    }
}