<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);

        $this->assertEquals($user->user_id, $cart->user->user_id);
    }

    public function test_cart_has_many_items(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
        ]);

        $this->assertCount(1, $cart->items);
    }

    public function test_item_count_attribute(): void
    {
        $cart = Cart::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product1->product_id,
            'quantity' => 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product2->product_id,
            'quantity' => 3,
        ]);

        $cart->load('items');

        $this->assertEquals(5, $cart->item_count);
    }

    public function test_subtotal_attribute(): void
    {
        $cart = Cart::factory()->create();
        $product1 = Product::factory()->create(['price' => 10.00]);
        $product2 = Product::factory()->create(['price' => 25.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product1->product_id,
            'quantity' => 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product2->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');

        // 2 * 10 + 1 * 25 = 45
        $this->assertEquals(45.00, $cart->subtotal);
    }

    public function test_total_attribute_without_discount(): void
    {
        $cart = Cart::factory()->create(['discount_amount' => 0]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');

        $this->assertEquals(50.00, $cart->total);
    }

    public function test_total_attribute_with_discount(): void
    {
        $cart = Cart::factory()->create(['discount_amount' => 10.00]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');

        $this->assertEquals(40.00, $cart->total);
    }

    public function test_total_cannot_be_negative(): void
    {
        $cart = Cart::factory()->create(['discount_amount' => 100.00]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');

        $this->assertEquals(0, $cart->total);
    }

    public function test_race_condition_exploited_detection(): void
    {
        $cart = Cart::factory()->create(['discount_amount' => 50.00]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');

        $this->assertTrue($cart->isRaceConditionExploited());
    }

    public function test_race_condition_not_exploited(): void
    {
        $cart = Cart::factory()->create(['discount_amount' => 10.00]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');

        $this->assertFalse($cart->isRaceConditionExploited());
    }

    public function test_recalculate_discount_removes_invalid_code(): void
    {
        $cart = Cart::factory()->create([
            'discount_code' => 'NONEXISTENT',
            'discount_amount' => 10.00,
        ]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');
        $cart->recalculateDiscount();
        $cart->refresh();

        $this->assertNull($cart->discount_code);
        $this->assertEquals(0, $cart->discount_amount);
    }

    public function test_recalculate_discount_removes_when_below_minimum(): void
    {
        $discountCode = DiscountCode::factory()->minimumPurchase(100.00)->create([
            'code' => 'BIGSPENDER',
        ]);

        $cart = Cart::factory()->create([
            'discount_code' => 'BIGSPENDER',
            'discount_amount' => 10.00,
        ]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $cart->load('items.product');
        $cart->recalculateDiscount();
        $cart->refresh();

        $this->assertNull($cart->discount_code);
        $this->assertEquals(0, $cart->discount_amount);
    }
}
