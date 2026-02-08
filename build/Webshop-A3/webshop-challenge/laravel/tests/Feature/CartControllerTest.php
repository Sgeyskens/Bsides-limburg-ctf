<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->cart = Cart::factory()->create(['user_id' => $this->user->user_id]);
    }

    public function test_guest_cannot_view_cart(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_cart(): void
    {
        $response = $this->actingAs($this->user)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $product = Product::factory()->movie()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 1,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart_item', [
            'cart_id' => $this->cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
    }

    public function test_adding_same_product_increases_quantity(): void
    {
        $product = Product::factory()->movie()->create();

        // Add first time
        $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 1,
            ]);

        // Add second time
        $response = $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 2,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_item', [
            'cart_id' => $this->cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 3,
        ]);
    }

    public function test_quantity_cannot_exceed_10(): void
    {
        $product = Product::factory()->movie()->create();

        // Add 8 first
        $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 8,
            ]);

        // Try to add 5 more (should cap at 10)
        $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 5,
            ]);

        $this->assertDatabaseHas('cart_item', [
            'cart_id' => $this->cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 10,
        ]);
    }

    public function test_tshirt_requires_size(): void
    {
        $product = Product::factory()->tshirt()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 1,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Please select a size for this item',
        ]);
    }

    public function test_tshirt_with_size_is_added(): void
    {
        $product = Product::factory()->tshirt()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 1,
                'size' => 'L',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart_item', [
            'cart_id' => $this->cart->cart_id,
            'product_id' => $product->product_id,
            'size' => 'L',
        ]);
    }

    public function test_user_can_update_cart_item_quantity(): void
    {
        $product = Product::factory()->create(['price' => 10.00]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $this->cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('cart.update', $cartItem), [
                'quantity' => 5,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart_item', [
            'cart_item_id' => $cartItem->cart_item_id,
            'quantity' => 5,
        ]);
    }

    public function test_user_cannot_update_another_users_cart_item(): void
    {
        $otherUser = User::factory()->create();
        $otherCart = Cart::factory()->create(['user_id' => $otherUser->user_id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $otherCart->cart_id,
            'product_id' => $product->product_id,
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('cart.update', $cartItem), [
                'quantity' => 5,
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_remove_cart_item(): void
    {
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $this->cart->cart_id,
            'product_id' => $product->product_id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('cart.remove', $cartItem));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('cart_item', [
            'cart_item_id' => $cartItem->cart_item_id,
        ]);
    }

    public function test_user_cannot_remove_another_users_cart_item(): void
    {
        $otherUser = User::factory()->create();
        $otherCart = Cart::factory()->create(['user_id' => $otherUser->user_id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $otherCart->cart_id,
            'product_id' => $product->product_id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('cart.remove', $cartItem));

        $response->assertStatus(403);
    }

    public function test_cart_count_endpoint(): void
    {
        $product = Product::factory()->create();
        CartItem::factory()->create([
            'cart_id' => $this->cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('cart.count'));

        $response->assertStatus(200);
        $response->assertJson(['count' => 3]);
    }
}
