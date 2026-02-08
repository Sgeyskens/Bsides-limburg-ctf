<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Order matters! Properties must be seeded before products
        $this->call([
            PropertySeeder::class,      // 1. Create properties first
            ProductSeeder::class,        // 2. Create products with properties
            DiscountCodeSeeder::class,   // 3. Create discount codes
            UserSeeder::class,           // 4. Create users
            OrderSeeder::class,          // 5. Create orders and cart items
            ProductRatingSeeder::class,  // 6. Create product ratings
        ]);

        $this->command->info('All seeders completed successfully!');
    }
}