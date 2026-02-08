<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\DiscountCodeUsage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index()
    {
        $cart = $this->getUserCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $cart->load('items.product');

        // NOTE: Removed recalculateDiscount() to preserve race condition exploits for CTF
        // $cart->recalculateDiscount();

        $subtotal = $this->calculateSubtotal($cart);

        // Calculate total including any applied discount
        $total = max($subtotal - $cart->discount_amount, 0);

        // Check if race condition was exploited (discount >= subtotal means free items!)
        $raceConditionFlag = null;
        if ($cart->discount_amount >= $subtotal && $cart->discount_amount > 0) {
            $raceConditionFlag = 'CTF{RACE_CONDITION_EXPLOITED_COUPON_STACKING}';
        }

        return view('checkout.index', compact('cart', 'subtotal', 'total', 'raceConditionFlag'));
    }

    /**
     * Apply discount code (AJAX) - VULNERABLE TO RACE CONDITION
     *
     * This method contains a deliberate race condition vulnerability for educational purposes.
     * Multiple concurrent requests from the SAME USER can apply the same discount multiple times.
     * Use Burp Suite Intruder with multiple threads to exploit.
     *
     * Normal usage is protected - users cannot manually apply a code twice.
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $cart = $this->getUserCart();
        $cart->load('items.product');
        $subtotal = $this->calculateSubtotal($cart);

        $validationError = $this->validateDiscountApplication($request->code, $subtotal);
        if ($validationError) {
            return response()->json([
                'success' => false,
                'message' => $validationError
            ], 422);
        }

        $discountCode = DiscountCode::where('code', strtoupper($request->code))->first();

        // Calculate the discount
        $discount = $discountCode->calculateDiscount($subtotal);
        $maxSafeDiscount = max($subtotal - 0.01, 0);
        $discount = min($discount, $maxSafeDiscount);

        // Browser protection: If request has _token_nonce, use UPDATE (sets value)
        // Burp Suite requests won't have this token, so they use INCREMENT (race condition)
        $token = $request->input('_token_nonce');
        if ($token) {
            // Browser request - check if already applied
            if ($cart->discount_code) {
                $total = $subtotal - $cart->discount_amount;
                return response()->json([
                    'success' => true,
                    'message' => 'Discount code applied',
                    'discount' => number_format($cart->discount_amount, 2),
                    'total' => number_format(max($total, 0), 2),
                    'subtotal' => number_format($subtotal, 2),
                    'code' => $cart->discount_code,
                    'percentage' => $discountCode->discount_percentage > 0 ? $discountCode->discount_percentage : null,
                    'flag' => null
                ]);
            }

            // Use UPDATE (sets absolute value) - no stacking possible
            DB::table('cart')
                ->where('cart_id', $cart->cart_id)
                ->update([
                    'discount_amount' => $discount,
                    'discount_code' => $discountCode->code
                ]);

            $total = $subtotal - $discount;
            return response()->json([
                'success' => true,
                'message' => 'Discount code applied',
                'discount' => number_format($discount, 2),
                'total' => number_format(max($total, 0), 2),
                'subtotal' => number_format($subtotal, 2),
                'code' => $discountCode->code,
                'percentage' => $discountCode->discount_percentage > 0 ? $discountCode->discount_percentage : null,
                'flag' => null
            ]);
        }

        // ⏰ RACE CONDITION VULNERABILITY (Burp Suite only) ⏰
        // No token = Burp Suite request - use vulnerable code path
        sleep(2); // Creates window for concurrent requests
        DB::table('cart')
            ->where('cart_id', $cart->cart_id)
            ->increment('discount_amount', $discount);

        DB::table('cart')
            ->where('cart_id', $cart->cart_id)
            ->update(['discount_code' => $discountCode->code]);

        $cart->refresh();

        // ⏰ RACE CONDITION WINDOW ENDS HERE ⏰

        $total = $subtotal - $cart->discount_amount;

        // Flag for successful race condition exploitation
        $raceConditionFlag = null;
        if ($total <= 0) {
            $raceConditionFlag = 'CTF{RACE_CONDITION_EXPLOITED_COUPON_STACKING}';
        }

        return response()->json([
            'success' => true,
            'message' => 'Discount code applied',
            'discount' => number_format($cart->discount_amount, 2),
            'total' => number_format(max($total, 0), 2),
            'subtotal' => number_format($subtotal, 2),
            'code' => $discountCode->code,
            'percentage' => $discountCode->discount_percentage > 0 ? $discountCode->discount_percentage : null,
            'flag' => $raceConditionFlag
        ]);
    }

    /**
     * Remove discount code (AJAX)
     */
    public function removeDiscount()
    {
        $cart = $this->getUserCart();

        // Release the claim on the discount code so other users can use it
        if ($cart->discount_code) {
            $discountCode = DiscountCode::where('code', $cart->discount_code)->first();
            if ($discountCode) {
                DiscountCodeUsage::releaseClaim($discountCode->code_id, Auth::id());
            }
        }

        $cart->discount_amount = 0;
        $cart->discount_code = null;
        $cart->save();

        $subtotal = $this->calculateSubtotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Discount code removed',
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($subtotal, 2)
        ]);
    }

    /**
     * Get cart status (for polling to detect race condition exploit)
     */
    public function status()
    {
        $cart = $this->getUserCart();
        $cart->load('items.product');
        $subtotal = $this->calculateSubtotal($cart);
        $total = max($subtotal - $cart->discount_amount, 0);

        $flag = null;
        if ($cart->discount_amount >= $subtotal && $cart->discount_amount > 0) {
            $flag = 'CTF{RACE_CONDITION_EXPLOITED_COUPON_STACKING}';
        }

        return response()->json([
            'discount_code' => $cart->discount_code,
            'discount_amount' => number_format($cart->discount_amount, 2),
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($total, 2),
            'flag' => $flag
        ]);
    }

    /**
     * Process the order
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_street' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            'billing_street' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
        ]);

        $cart = $this->getUserCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $cart->load('items.product');

        // Recalculate discount before processing order to prevent race condition exploitation
        $cart->recalculateDiscount();
        $cart->refresh();

        $subtotal = $this->calculateSubtotal($cart);

        // Use the properly recalculated discount
        $discount = $cart->discount_amount;
        $discountCodeString = $cart->discount_code;

        // Total cannot be negative
        $total = max($subtotal - $discount, 0);

        // Combine address fields into full address strings
        $shippingAddress = implode(', ', [
            $request->shipping_street,
            $request->shipping_city,
            $request->shipping_state,
            $request->shipping_zip,
            $request->shipping_country,
        ]);

        $billingAddress = implode(', ', [
            $request->billing_street,
            $request->billing_city,
            $request->billing_state,
            $request->billing_zip,
            $request->billing_country,
        ]);

        DB::beginTransaction();

        try {
            // Create the order
            $order = Order::create([
                'user_id' => Auth::user()->user_id,
                'order_date' => now(),
                'total_amount' => $total,
                'status' => 'processing',
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'discount_code' => $discountCodeString,
            ]);

            // Create order items (snapshot product data)
            foreach ($cart->items as $cartItem) {
                if ($cartItem->product) {
                    $order->items()->create([
                        'product_id' => $cartItem->product_id,
                        'name' => $cartItem->product->name,
                        'quantity' => $cartItem->quantity,
                        'price_per_unit' => $cartItem->product->price,
                        'size' => $cartItem->size,
                    ]);
                }
            }

            // Increment discount code usage if applicable
            if ($discountCodeString) {
                $discountCode = DiscountCode::where('code', $discountCodeString)->first();
                if ($discountCode) {
                    $discountCode->incrementUsage();
                    // NOTE: We intentionally do NOT release the claim here
                    // The DiscountCodeUsage record persists to prevent the same user
                    // from using the same discount code on future orders
                }
            }

            // Clear the cart and reset discount
            $cart->items()->delete();
            $cart->discount_amount = 0;
            $cart->discount_code = null;
            $cart->save();

            DB::commit();

            return redirect()->route('order.confirmation', $order)
                ->with('success', 'Your order has been placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('checkout.index')
                ->with('error', 'There was an error processing your order. Please try again.');
        }
    }

    /**
     * Get the current user's cart
     */
    private function getUserCart(): ?Cart
    {
        return Auth::user()->cart;
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

    /**
     * Validate discount code application and return error message if invalid
     */
    private function validateDiscountApplication(string $code, float $subtotal): ?string
    {
        $discountCode = DiscountCode::where('code', strtoupper($code))->first();

        return match (true) {
            !$discountCode => 'Invalid discount code',
            !$discountCode->isValid() => 'This discount code has expired or reached its usage limit',
            !$discountCode->canApplyToAmount($subtotal) => "Cart total must be at least $" . number_format($discountCode->minimum_purchase, 2) . " to use this coupon",
            default => null,
        };
    }
}

