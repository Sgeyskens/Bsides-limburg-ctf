<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product');

        // Recalculate discount to ensure it's valid for current cart contents
        $cart->recalculateDiscount();

        $subtotal = $this->calculateSubtotal($cart);

        return view('cart.index', compact('cart', 'subtotal'));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'integer|min:1|max:10',
            'size' => 'nullable|string|in:S,M,L,XL,XXL',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Only clothing items (shirts) require a size
        $requiresSize = $product->product_type === 'merch' &&
                        stripos($product->name, 'shirt') !== false;

        if ($requiresSize && empty($request->size)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a size for this item'
            ], 422);
        }

        $cart = $this->getOrCreateCart();
        $quantity = $request->quantity ?? 1;

        // Check if item already exists in cart (same product and size)
        $existingItem = $cart->items()
            ->where('product_id', $product->product_id)
            ->where('size', $request->size)
            ->first();

        if ($existingItem) {
            $newQuantity = min($existingItem->quantity + $quantity, 10);
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'size' => $request->size,
                'added_date' => now(),
            ]);
        }

        $cart->update(['last_updated' => now()]);
        $cart->load('items.product');
        $cart->recalculateDiscount();

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cartCount' => $cart->fresh()->item_count
        ]);
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function update(Request $request, CartItem $cartItem)
    {
        // Verify the item belongs to the user's cart
        $cart = $this->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->cart_id) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in your cart'
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cartItem->update(['quantity' => $request->quantity]);
        $cart->update(['last_updated' => now()]);

        $cart->load('items.product');
        $cart->recalculateDiscount();
        $subtotal = $this->calculateSubtotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'cartCount' => $cart->item_count,
            'itemTotal' => number_format($cartItem->product->price * $cartItem->quantity, 2),
            'subtotal' => number_format($subtotal, 2),
            'discountAmount' => number_format($cart->discount_amount, 2),
            'discountRemoved' => empty($cart->discount_code)
        ]);
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove(CartItem $cartItem)
    {
        // Verify the item belongs to the user's cart
        $cart = $this->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->cart_id) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in your cart'
            ], 403);
        }

        $cartItem->delete();
        $cart->update(['last_updated' => now()]);

        $cart->load('items.product');
        $cart->recalculateDiscount();
        $subtotal = $this->calculateSubtotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cartCount' => $cart->item_count,
            'subtotal' => number_format($subtotal, 2),
            'discountAmount' => number_format($cart->discount_amount, 2),
            'discountRemoved' => empty($cart->discount_code)
        ]);
    }

    /**
     * Get cart count (AJAX)
     */
    public function count()
    {
        $cart = Auth::user()->cart;
        $count = $cart ? $cart->item_count : 0;

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get or create cart for current user
     */
    private function getOrCreateCart(): Cart
    {
        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->user_id,
                'created_date' => now(),
                'last_updated' => now(),
            ]);
        }

        return $cart;
    }

    /**
     * Calculate cart subtotal
     */
    private function calculateSubtotal(Cart $cart): float
    {
        return $cart->items->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });
    }
}
