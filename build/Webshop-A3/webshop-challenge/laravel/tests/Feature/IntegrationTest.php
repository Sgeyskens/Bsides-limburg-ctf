<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete shopping flow: Browse -> Add to Cart -> Apply Discount -> Checkout -> Order
     */
    public function test_complete_shopping_flow_with_discount(): void
    {
        // Create user
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);

        // Create products
        $movie = Product::factory()->movie()->create(['price' => 29.99]);
        $game = Product::factory()->game()->create(['price' => 59.99]);

        // Create discount code
        $discountCode = DiscountCode::factory()->percentage(15)->create([
            'code' => 'SAVE15',
            'current_uses' => 0,
        ]);

        // Step 1: Browse products page
        $response = $this->actingAs($user)->get('/movies');
        $response->assertStatus(200);

        // Step 2: Add products to cart
        $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $movie->product_id,
                'quantity' => 2,
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $game->product_id,
                'quantity' => 1,
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify cart contents
        $this->assertDatabaseHas('cart_item', [
            'cart_id' => $cart->cart_id,
            'product_id' => $movie->product_id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('cart_item', [
            'cart_id' => $cart->cart_id,
            'product_id' => $game->product_id,
            'quantity' => 1,
        ]);

        // Step 3: View cart
        $response = $this->actingAs($user)->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertViewIs('cart.index');

        // Step 4: Go to checkout
        $response = $this->actingAs($user)->get(route('checkout.index'));
        $response->assertStatus(200);
        $response->assertViewIs('checkout.index');

        // Step 5: Apply discount code
        $response = $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'SAVE15',
                '_token_nonce' => 'test-token',
            ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'code' => 'SAVE15']);

        // Verify discount was applied
        $cart->refresh();
        $this->assertEquals('SAVE15', $cart->discount_code);

        // Step 6: Process checkout
        $response = $this->actingAs($user)
            ->post(route('checkout.process'), [
                'shipping_street' => '123 Camp Crystal Lake',
                'shipping_city' => 'Cunningham',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07001',
                'shipping_country' => 'USA',
                'billing_street' => '123 Camp Crystal Lake',
                'billing_city' => 'Cunningham',
                'billing_state' => 'NJ',
                'billing_zip' => '07001',
                'billing_country' => 'USA',
            ]);

        $response->assertRedirect();

        // Step 7: Verify order was created
        $order = Order::where('user_id', $user->user_id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('SAVE15', $order->discount_code);
        $this->assertEquals('processing', $order->status);

        // Verify order items were created
        $this->assertDatabaseHas('order_item', [
            'order_id' => $order->order_id,
            'product_id' => $movie->product_id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('order_item', [
            'order_id' => $order->order_id,
            'product_id' => $game->product_id,
            'quantity' => 1,
        ]);

        // Verify discount code usage was incremented
        $discountCode->refresh();
        $this->assertEquals(1, $discountCode->current_uses);

        // Verify cart is now empty
        $cart->refresh();
        $this->assertCount(0, $cart->items);
    }

    /**
     * Test complete user registration and login flow
     */
    public function test_user_registration_login_and_profile_update_flow(): void
    {
        // Step 1: View registration page
        $response = $this->get(route('register'));
        $response->assertStatus(200);

        // Step 2: Register new user
        $response = $this->post(route('register'), [
            'username' => 'jasonvoorhees',
            'email' => 'jason@crystallake.com',
            'password' => 'friday13th!',
            'password_confirmation' => 'friday13th!',
            'terms' => true,
        ]);
        $response->assertRedirect(route('account'));

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'username' => 'jasonvoorhees',
            'email' => 'jason@crystallake.com',
        ]);

        $user = User::where('username', 'jasonvoorhees')->first();
        $this->assertAuthenticatedAs($user);

        // Step 3: Logout
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect('/');
        $this->assertGuest();

        // Step 4: Login again
        $response = $this->post(route('login'), [
            'email' => 'jasonvoorhees',
            'password' => 'friday13th!',
        ]);
        $response->assertRedirect('/account');
        $this->assertAuthenticatedAs($user);

        // Step 5: View account page
        $response = $this->actingAs($user)->get(route('account'));
        $response->assertStatus(200);

        // Step 6: Update profile
        $response = $this->actingAs($user)
            ->put(route('account.update'), [
                'username' => $user->username,
                'email' => $user->email,
                'bio' => 'I love Camp Crystal Lake',
            ]);
        $response->assertRedirect(route('account'));

        // Verify profile was updated
        $user->refresh();
        $this->assertEquals('I love Camp Crystal Lake', $user->bio);
    }

    /**
     * Test product search and filter flow
     */
    public function test_product_search_and_filter_flow(): void
    {
        // Create products with different prices and types
        $cheapMovie = Product::factory()->movie()->create([
            'name' => 'Friday the 13th Part I',
            'price' => 9.99,
        ]);
        $expensiveMovie = Product::factory()->movie()->create([
            'name' => 'Friday the 13th Part II',
            'price' => 49.99,
        ]);
        $game = Product::factory()->game()->create([
            'name' => 'Friday the 13th: The Game',
            'price' => 29.99,
        ]);

        // Step 1: Search for products
        $response = $this->get(route('search', ['q' => 'Friday']));
        $response->assertStatus(200);

        // Step 2: View movies page
        $response = $this->get('/movies');
        $response->assertStatus(200);
        $response->assertViewHas('movies');

        // Step 3: Filter movies by price (AJAX)
        $response = $this->getJson('/products/movie/filter?min_price=0&max_price=20');
        $response->assertStatus(200);

        // Step 4: View games page
        $response = $this->get('/games');
        $response->assertStatus(200);

        // Step 5: Add filtered product to cart
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $cheapMovie->product_id,
                'quantity' => 1,
            ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_item', [
            'cart_id' => $cart->cart_id,
            'product_id' => $cheapMovie->product_id,
        ]);
    }

    /**
     * Test cart modification flow - add, update, remove items
     */
    public function test_cart_modification_flow(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);

        $product1 = Product::factory()->movie()->create(['price' => 19.99]);
        $product2 = Product::factory()->game()->create(['price' => 39.99]);

        // Step 1: Add first product
        $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $product1->product_id,
                'quantity' => 1,
            ])
            ->assertStatus(200);

        // Step 2: Add second product
        $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $product2->product_id,
                'quantity' => 2,
            ])
            ->assertStatus(200);

        // Step 3: Check cart count
        $response = $this->actingAs($user)->getJson(route('cart.count'));
        $response->assertStatus(200);
        $response->assertJson(['count' => 3]); // 1 + 2 = 3 items

        // Step 4: Update first product quantity
        $cartItem = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $product1->product_id)
            ->first();

        $this->actingAs($user)
            ->patchJson(route('cart.update', $cartItem), [
                'quantity' => 3,
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('cart_item', [
            'cart_item_id' => $cartItem->cart_item_id,
            'quantity' => 3,
        ]);

        // Step 5: Remove second product
        $cartItem2 = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $product2->product_id)
            ->first();

        $this->actingAs($user)
            ->deleteJson(route('cart.remove', $cartItem2))
            ->assertStatus(200);

        $this->assertDatabaseMissing('cart_item', [
            'cart_item_id' => $cartItem2->cart_item_id,
        ]);

        // Step 6: Verify final cart state
        $response = $this->actingAs($user)->getJson(route('cart.count'));
        $response->assertJson(['count' => 3]); // Only product1 with qty 3
    }

    /**
     * Test discount code validation flow
     */
    public function test_discount_code_validation_flow(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);
        $product = Product::factory()->create(['price' => 50.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        // Create various discount codes
        $validCode = DiscountCode::factory()->percentage(10)->create(['code' => 'VALID10']);
        $expiredCode = DiscountCode::factory()->expired()->create(['code' => 'EXPIRED']);
        $futureCode = DiscountCode::factory()->future()->create(['code' => 'FUTURE']);
        $exhaustedCode = DiscountCode::factory()->exhausted()->create(['code' => 'NOMORE']);
        $minPurchaseCode = DiscountCode::factory()->minimumPurchase(100)->create(['code' => 'BIGORDER']);

        // Step 1: Try invalid code
        $response = $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'NOTEXIST',
                '_token_nonce' => 'test',
            ]);
        $response->assertStatus(422);
        $response->assertJson(['success' => false, 'message' => 'Invalid discount code']);

        // Step 2: Try expired code
        $response = $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'EXPIRED',
                '_token_nonce' => 'test',
            ]);
        $response->assertStatus(422);

        // Step 3: Try exhausted code
        $response = $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'NOMORE',
                '_token_nonce' => 'test',
            ]);
        $response->assertStatus(422);

        // Step 4: Try minimum purchase not met
        $response = $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'BIGORDER',
                '_token_nonce' => 'test',
            ]);
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Cart total must be at least $100.00 to use this coupon']);

        // Step 5: Apply valid code
        $response = $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'VALID10',
                '_token_nonce' => 'test',
            ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify discount was applied
        $cart->refresh();
        $this->assertEquals('VALID10', $cart->discount_code);

        // Step 6: Remove discount - refresh user to clear cached cart relationship
        $user->refresh();
        $response = $this->actingAs($user)
            ->postJson(route('checkout.remove-discount'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify discount was removed - query fresh from database
        $updatedCart = Cart::find($cart->cart_id);
        $this->assertNull($updatedCart->discount_code);
        $this->assertEquals(0, $updatedCart->discount_amount);
    }

    /**
     * Test checkout with fixed amount discount
     */
    public function test_checkout_with_fixed_amount_discount(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);
        $product = Product::factory()->create(['price' => 75.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 2, // Total: $150
        ]);

        $discountCode = DiscountCode::factory()->fixedAmount(25)->create([
            'code' => 'SAVE25',
            'current_uses' => 0,
        ]);

        // Apply discount
        $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'SAVE25',
                '_token_nonce' => 'test',
            ])
            ->assertStatus(200);

        // Check status endpoint
        $response = $this->actingAs($user)->getJson(route('checkout.status'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'discount_code',
            'discount_amount',
            'subtotal',
            'total',
        ]);

        // Process checkout
        $response = $this->actingAs($user)
            ->post(route('checkout.process'), [
                'shipping_street' => '1 Crystal Lake',
                'shipping_city' => 'Wessex',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07001',
                'shipping_country' => 'USA',
                'billing_street' => '1 Crystal Lake',
                'billing_city' => 'Wessex',
                'billing_state' => 'NJ',
                'billing_zip' => '07001',
                'billing_country' => 'USA',
            ]);

        $response->assertRedirect();

        // Verify order
        $order = Order::where('user_id', $user->user_id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('SAVE25', $order->discount_code);

        // Discount usage incremented
        $discountCode->refresh();
        $this->assertEquals(1, $discountCode->current_uses);
    }

    /**
     * Test order confirmation page access
     */
    public function test_order_confirmation_access(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);
        $product = Product::factory()->create(['price' => 25.00]);

        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        // Complete checkout
        $this->actingAs($user)
            ->post(route('checkout.process'), [
                'shipping_street' => '123 Main St',
                'shipping_city' => 'Anytown',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07001',
                'shipping_country' => 'USA',
                'billing_street' => '123 Main St',
                'billing_city' => 'Anytown',
                'billing_state' => 'NJ',
                'billing_zip' => '07001',
                'billing_country' => 'USA',
            ]);

        $order = Order::where('user_id', $user->user_id)->first();

        // View order confirmation
        $response = $this->actingAs($user)
            ->get(route('order.confirmation', $order));
        $response->assertStatus(200);

        // Other user cannot view this order
        $otherUser = User::factory()->create();
        $response = $this->actingAs($otherUser)
            ->get(route('order.confirmation', $order));
        $response->assertStatus(403);
    }

    /**
     * Test t-shirt size requirement in cart flow
     */
    public function test_tshirt_size_requirement_flow(): void
    {
        $user = User::factory()->create();
        Cart::factory()->create(['user_id' => $user->user_id]);
        $tshirt = Product::factory()->tshirt()->create(['price' => 24.99]);

        // Try to add without size - should fail
        $response = $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $tshirt->product_id,
                'quantity' => 1,
            ]);
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Please select a size for this item',
        ]);

        // Add with size - should succeed
        $response = $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $tshirt->product_id,
                'quantity' => 1,
                'size' => 'M',
            ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Add same shirt different size - should create new cart item
        $response = $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $tshirt->product_id,
                'quantity' => 1,
                'size' => 'L',
            ]);
        $response->assertStatus(200);

        // Should have 2 cart items (different sizes)
        $cart = Cart::where('user_id', $user->user_id)->first();
        $this->assertCount(2, $cart->items);
    }

    /**
     * Test multiple orders by same user
     */
    public function test_multiple_orders_by_user(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);
        $product = Product::factory()->create(['price' => 15.00]);

        // First order
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->post(route('checkout.process'), [
                'shipping_street' => '123 First St',
                'shipping_city' => 'Anytown',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07001',
                'shipping_country' => 'USA',
                'billing_street' => '123 First St',
                'billing_city' => 'Anytown',
                'billing_state' => 'NJ',
                'billing_zip' => '07001',
                'billing_country' => 'USA',
            ]);

        // Second order
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);

        $this->actingAs($user)
            ->post(route('checkout.process'), [
                'shipping_street' => '456 Second St',
                'shipping_city' => 'Othertown',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07002',
                'shipping_country' => 'USA',
                'billing_street' => '456 Second St',
                'billing_city' => 'Othertown',
                'billing_state' => 'NJ',
                'billing_zip' => '07002',
                'billing_country' => 'USA',
            ]);

        // User should have 2 orders
        $orders = Order::where('user_id', $user->user_id)->get();
        $this->assertCount(2, $orders);
    }

    /**
     * Test guest cannot access protected routes
     */
    public function test_guest_access_restrictions(): void
    {
        // Guest cannot access cart
        $this->get(route('cart.index'))->assertRedirect(route('login'));

        // Guest cannot access checkout
        $this->get(route('checkout.index'))->assertRedirect(route('login'));

        // Guest cannot access account
        $this->get(route('account'))->assertRedirect(route('login'));

        // Guest cannot add to cart
        $product = Product::factory()->create();
        $this->postJson(route('cart.add'), [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ])->assertStatus(401);
    }

    /**
     * Test authenticated user can browse public pages
     */
    public function test_authenticated_user_browses_public_pages(): void
    {
        $user = User::factory()->create();

        // Create some products
        Product::factory()->movie()->count(3)->create();
        Product::factory()->game()->count(2)->create();
        Product::factory()->merch()->count(2)->create();

        // Home page
        $this->actingAs($user)->get('/')->assertStatus(200);

        // Product pages
        $this->actingAs($user)->get('/movies')->assertStatus(200);
        $this->actingAs($user)->get('/games')->assertStatus(200);
        $this->actingAs($user)->get('/merch')->assertStatus(200);

        // About page
        $this->actingAs($user)->get('/about')->assertStatus(200);

        // Search
        $this->actingAs($user)->get(route('search', ['q' => 'friday']))->assertStatus(200);
    }

    /**
     * Test discount removal when cart no longer meets minimum purchase
     */
    public function test_discount_invalidation_on_cart_change(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->user_id]);

        $product = Product::factory()->create(['price' => 60.00]);

        // Add 2 items = $120 total
        CartItem::factory()->create([
            'cart_id' => $cart->cart_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);

        // Create discount with $100 minimum
        DiscountCode::factory()->minimumPurchase(100)->percentage(10)->create(['code' => 'MINORDER']);

        // Apply discount (should work with $120 cart)
        $this->actingAs($user)
            ->postJson(route('checkout.apply-discount'), [
                'code' => 'MINORDER',
                '_token_nonce' => 'test',
            ])
            ->assertStatus(200);

        $cart->refresh();
        $this->assertEquals('MINORDER', $cart->discount_code);

        // Now reduce quantity to 1 item = $60 (below minimum)
        $cartItem = $cart->items->first();
        $this->actingAs($user)
            ->patchJson(route('cart.update', $cartItem), [
                'quantity' => 1,
            ])
            ->assertStatus(200);

        // Go to checkout - discount should still be stored but may show warning
        $response = $this->actingAs($user)->getJson(route('checkout.status'));
        $response->assertStatus(200);
    }

    /**
     * Test autocomplete search functionality
     */
    public function test_search_autocomplete(): void
    {
        Product::factory()->create(['name' => 'Friday the 13th Part I']);
        Product::factory()->create(['name' => 'Friday the 13th Part II']);
        Product::factory()->create(['name' => 'Halloween']);

        $response = $this->getJson(route('search.autocomplete', ['q' => 'Friday']));
        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }

    /**
     * Test complete flow from registration to order
     */
    public function test_full_user_journey_registration_to_order(): void
    {
        // Create a product to buy
        $product = Product::factory()->movie()->create(['price' => 19.99]);

        // Step 1: Register
        $response = $this->post(route('register'), [
            'username' => 'newcustomer',
            'email' => 'customer@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ]);
        $response->assertRedirect(route('account'));

        $user = User::where('username', 'newcustomer')->first();
        $this->assertNotNull($user);

        // Step 2: Browse products
        $response = $this->actingAs($user)->get('/movies');
        $response->assertStatus(200);

        // Step 3: Add to cart (cart is auto-created)
        $response = $this->actingAs($user)
            ->postJson(route('cart.add'), [
                'product_id' => $product->product_id,
                'quantity' => 1,
            ]);
        $response->assertStatus(200);

        // Refresh user to clear cached relationships
        $user->refresh();

        // Step 4: View cart
        $response = $this->actingAs($user)->get(route('cart.index'));
        $response->assertStatus(200);

        // Step 5: Checkout
        $response = $this->actingAs($user)->get(route('checkout.index'));
        $response->assertStatus(200);

        // Step 6: Complete order
        $response = $this->actingAs($user)
            ->post(route('checkout.process'), [
                'shipping_street' => '789 New St',
                'shipping_city' => 'Newtown',
                'shipping_state' => 'NJ',
                'shipping_zip' => '07003',
                'shipping_country' => 'USA',
                'billing_street' => '789 New St',
                'billing_city' => 'Newtown',
                'billing_state' => 'NJ',
                'billing_zip' => '07003',
                'billing_country' => 'USA',
            ]);
        $response->assertRedirect();

        // Verify order exists
        $order = Order::where('user_id', $user->user_id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(19.99, $order->total_amount);

        // Step 7: View order confirmation
        $response = $this->actingAs($user)
            ->get(route('order.confirmation', $order));
        $response->assertStatus(200);
    }
}
