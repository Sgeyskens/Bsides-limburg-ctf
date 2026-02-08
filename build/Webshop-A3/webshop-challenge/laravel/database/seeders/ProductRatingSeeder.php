<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductRating;
use Illuminate\Support\Facades\DB;

class ProductRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample ratings for testing the rating filter functionality.
     */
    public function run(): void
    {
        // Get all products and users
        $products = Product::all();
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        // Sample reviews for variety
        $reviews = [
            5 => [
                'Absolutely terrifying! A must-have for any horror fan.',
                'Perfect condition and fast delivery. 10/10!',
                'This is exactly what I was looking for. Amazing quality!',
                'Camp Crystal Lake never looked so good. Love it!',
            ],
            4 => [
                'Great product, very happy with my purchase.',
                'Really good quality. Minor packaging issues but overall great.',
                'Jason would be proud. Solid product!',
                'Excellent addition to my horror collection.',
            ],
            3 => [
                'Decent product. Does what it says.',
                'Average quality, but worth the price.',
                'It\'s okay. Expected a bit more.',
                'Not bad, not great. Somewhere in the middle.',
            ],
            2 => [
                'Could be better. Quality wasn\'t as expected.',
                'Disappointing experience. Expected more.',
                'The product is fine but arrived damaged.',
            ],
            1 => [
                'Not what I expected at all.',
                'Poor quality. Would not recommend.',
            ],
        ];

        $ratingsCreated = 0;

        foreach ($products as $product) {
            // Generate 2-6 random ratings per product
            $numRatings = rand(2, 6);

            // Shuffle users to get random reviewers
            $reviewers = $users->shuffle()->take(min($numRatings, $users->count()));

            foreach ($reviewers as $user) {
                // Weight ratings towards 4-5 stars (more realistic)
                $rating = $this->getWeightedRating();

                // Get a random review for this rating
                $reviewOptions = $reviews[$rating] ?? $reviews[3];
                $review = $reviewOptions[array_rand($reviewOptions)];

                try {
                    ProductRating::create([
                        'product_id' => $product->product_id,
                        'user_id' => $user->user_id,
                        'rating' => $rating,
                        'review' => $review,
                    ]);
                    $ratingsCreated++;
                } catch (\Exception $e) {
                    // Skip if duplicate (user already rated this product)
                    continue;
                }
            }
        }

        $this->command->info("Product ratings seeded successfully! Created {$ratingsCreated} ratings.");
    }

    /**
     * Get a weighted random rating (more likely to be 4-5 stars).
     */
    private function getWeightedRating(): int
    {
        $weights = [
            5 => 35,  // 35% chance
            4 => 30,  // 30% chance
            3 => 20,  // 20% chance
            2 => 10,  // 10% chance
            1 => 5,   // 5% chance
        ];

        $total = array_sum($weights);
        $rand = rand(1, $total);

        $cumulative = 0;
        foreach ($weights as $rating => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $rating;
            }
        }

        return 4; // Default fallback
    }
}
