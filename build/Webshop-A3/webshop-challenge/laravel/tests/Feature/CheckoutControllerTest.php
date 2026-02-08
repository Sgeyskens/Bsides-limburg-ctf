<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Cart $cart;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->cart = Cart::factory()->create(['user_id' => $this->user->user_id]);
        $this->product = Product::factory()->create(['price' => 100.00]);

        CartItem::factory()->create([
            'cart_id' => $this->cart->cart_id,
            'product_id' => $this->product->product_id,
            'quantity' => 1,
        ]);
    }

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_with_empty_cart_is_redirected(): void
    {
        $user = User::factory()->create();
        Cart::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertRedirect(route('cart.index'));
    }

    public function test_user_with_items_can_view_checkout(): void
    {
        $response = $this->actingAs($this->user)->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout.index');
        $response->assertViewHas('subtotal', 100.00);
    }

    public function test_user_can_apply_valid_discount_code(): void
    {
        $discountCode = DiscountCode::factory()->percentage(10)->create([
            'code' => 'FRIDAY13',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'FRIDAY13',
                '_token_nonce' => 'test-token',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'code' => 'FRIDAY13',
        ]);

        $this->cart->refresh();
        $this->assertEquals('FRIDAY13', $this->cart->discount_code);
    }

    public function test_invalid_discount_code_returns_error(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'INVALIDCODE',
                '_token_nonce' => 'test-token',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid discount code',
        ]);
    }

    public function test_expired_discount_code_returns_error(): void
    {
        DiscountCode::factory()->expired()->create([
            'code' => 'EXPIRED',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'EXPIRED',
                '_token_nonce' => 'test-token',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'This discount code has expired or reached its usage limit',
        ]);
    }

    public function test_exhausted_discount_code_returns_error(): void
    {
        DiscountCode::factory()->exhausted()->create([
            'code' => 'EXHAUSTED',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'EXHAUSTED',
                '_token_nonce' => 'test-token',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'This discount code has expired or reached its usage limit',
        ]);
    }

    public function test_minimum_purchase_not_met_returns_error(): void
    {
        DiscountCode::factory()->minimumPurchase(200.00)->create([
            'code' => 'BIGSPENDER',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'BIGSPENDER',
                '_token_nonce' => 'test-token',
            ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $response->assertJsonFragment(['message' => 'Cart total must be at least $200.00 to use this coupon']);
    }

    public function test_user_can_remove_discount(): void
    {
        $this->cart->update([
            'discount_code' => 'FRIDAY13',
            'discount_amount' => 10.00,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('checkout.remove-discount'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->cart->refresh();
        $this->assertNull($this->cart->discount_code);
        $this->assertEquals(0, $this->cart->discount_amount);
    }

    public function test_checkout_status_endpoint(): void
    {
        $this->cart->update([
            'discount_code' => 'TEST',
            'discount_amount' => 10.00,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('checkout.status'));

        $response->assertStatus(200);
        $response->assertJson([
            'discount_code' => 'TEST',
            'discount_amount' => '10.00',
            'subtotal' => '100.00',
            'total' => '90.00',
        ]);
    }

    public function test_checkout_process_creates_order(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('checkout.process'), [
                'shipping_street' => '123 Crystal Lake',
                'shipping_city' => 'Cunningham',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07001',
                'shipping_country' => 'USA',
                'billing_street' => '123 Crystal Lake',
                'billing_city' => 'Cunningham',
                'billing_state' => 'NJ',
                'billing_zip' => '07001',
                'billing_country' => 'USA',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->user_id,
            'total_amount' => 100.00,
            'status' => 'processing',
        ]);

        // Cart should be empty after checkout
        $this->cart->refresh();
        $this->assertCount(0, $this->cart->items);
    }

    public function test_checkout_process_applies_discount(): void
    {
        $discountCode = DiscountCode::factory()->percentage(20)->create([
            'code' => 'SAVE20',
            'current_uses' => 0,
        ]);

        $this->cart->update([
            'discount_code' => 'SAVE20',
            'discount_amount' => 20.00,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('checkout.process'), [
                'shipping_street' => '123 Crystal Lake',
                'shipping_city' => 'Cunningham',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07001',
                'shipping_country' => 'USA',
                'billing_street' => '123 Crystal Lake',
                'billing_city' => 'Cunningham',
                'billing_state' => 'NJ',
                'billing_zip' => '07001',
                'billing_country' => 'USA',
            ]);

        $response->assertRedirect();

        // Order total should reflect discount (100 - 20 = 80, but recalculated as 100 - 19.99 = 80.01)
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->user_id,
            'discount_code' => 'SAVE20',
        ]);

        // Discount code usage should be incremented
        $discountCode->refresh();
        $this->assertEquals(1, $discountCode->current_uses);
    }

    public function test_checkout_process_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('checkout.process'), []);

        $response->assertSessionHasErrors([
            'shipping_street',
            'shipping_city',
            'shipping_state',
            'shipping_zip',
            'shipping_country',
            'billing_street',
            'billing_city',
            'billing_state',
            'billing_zip',
            'billing_country',
        ]);
    }
}
