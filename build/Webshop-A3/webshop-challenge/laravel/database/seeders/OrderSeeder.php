<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    // Define constant for repeated address
    private const JASON_ADDRESS = '123 Crystal Lake Road, Blairstown, NJ 07825';

    public function run(): void
    {
        // Get user IDs
        $users = DB::table('users')->pluck('user_id', 'username');
        $products = DB::table('products')->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Skipping order seeder.');
            return;
        }

        // Create orders for jason_voorhees (user_id 2 fallback)
        $userId = $users['jason_voorhees'] ?? 2;

        // Check if orders already exist for this user (seeder already ran)
        $existingOrders = DB::table('orders')->where('user_id', $userId)->exists();
        if ($existingOrders) {
            $this->command->info('Orders already exist, skipping order seeder.');
            return;
        }

        $orders = [
            [
                'user_id' => $userId,
                'order_date' => Carbon::now()->subDays(5),
                'total_amount' => 8998, // stored in cents
                'status' => 'delivered',
                'shipping_address' => self::JASON_ADDRESS,
                'billing_address' => self::JASON_ADDRESS,
                'tracking_number' => 'USPS123456789',
            ],
            [
                'user_id' => $userId,
                'order_date' => Carbon::now()->subDays(10),
                'total_amount' => 5999,
                'status' => 'shipped',
                'shipping_address' => self::JASON_ADDRESS,
                'billing_address' => self::JASON_ADDRESS,
                'tracking_number' => 'USPS987654321',
            ],
            [
                'user_id' => $userId,
                'order_date' => Carbon::now()->subDays(2),
                'total_amount' => 9798,
                'status' => 'processing',
                'shipping_address' => self::JASON_ADDRESS,
                'billing_address' => self::JASON_ADDRESS,
                'tracking_number' => null,
            ],
        ];

        foreach ($orders as $order) {
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $order['user_id'],
                'order_date' => $order['order_date'],
                'total_amount' => $order['total_amount'],
                'status' => $order['status'],
                'shipping_address' => $order['shipping_address'],
                'billing_address' => $order['billing_address'],
                'tracking_number' => $order['tracking_number'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add random items to each order
            $randomProducts = $products->random(rand(1, 3));
            foreach ($randomProducts as $product) {
                DB::table('order_item')->insert([
                    'order_id' => $orderId,
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'quantity' => rand(1, 2),
                    'price_per_unit' => $product->price,
                    'size' => $product->product_type === 'merch' ? ['S', 'M', 'L', 'XL'][rand(0, 3)] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create cart for jason_voorhees with some items
        $cartId = DB::table('cart')->insertGetId([
            'user_id' => $userId,
            'created_date' => Carbon::now()->subDays(1),
            'last_updated' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add 3 random products to cart
        $cartProducts = $products->random(3);
        foreach ($cartProducts as $product) {
            DB::table('cart_item')->insert([
                'cart_id' => $cartId,
                'product_id' => $product->product_id,
                'quantity' => rand(1, 2),
                'size' => $product->product_type === 'merch' ? ['S', 'M', 'L', 'XL'][rand(0, 3)] : null,
                'added_date' => Carbon::now()->subDays(rand(0, 3)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Orders and cart seeded successfully!');
    }
}
