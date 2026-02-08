<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';
    protected $primaryKey = 'cart_id';

    // Disable default timestamps since we use custom column names
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'created_date',
        'last_updated',
        'discount_code',
        'discount_amount',
    ];

    protected $casts = [
        'created_date' => 'date',
        'last_updated' => 'datetime',
        'discount_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Calculate the subtotal (before discount)
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });
    }

    /**
     * Calculate the total (after discount)
     * This will be 0 or negative if the race condition is successfully exploited
     */
    public function getTotalAttribute()
    {
        return max($this->subtotal - $this->discount_amount, 0);
    }

    /**
     * Check if the race condition has been exploited
     * (discount is greater than or equal to subtotal)
     */
    public function isRaceConditionExploited()
    {
        return $this->discount_amount >= $this->subtotal && $this->discount_amount > 0;
    }

    /**
     * Recalculate the discount amount based on current cart subtotal.
     * Removes discount if cart no longer meets minimum purchase requirement.
     * Ensures discount never brings total to $0 (only race condition can achieve that).
     */
    public function recalculateDiscount(): void
    {
        // Skip if no discount is applied
        if (empty($this->discount_code)) {
            return;
        }

        $discountCode = DiscountCode::where('code', $this->discount_code)->first();

        if (!$discountCode) {
            // Discount code no longer exists, remove it
            $this->clearDiscount();
            return;
        }

        // Calculate subtotal by loading fresh items from database
        $items = CartItem::where('cart_id', $this->cart_id)->with('product')->get();
        $subtotal = $items->sum(function ($item) {
            return $item->product ? $item->product->price * $item->quantity : 0;
        });

        // Check if cart still meets minimum purchase requirement
        if ($subtotal <= 0 || !$discountCode->canApplyToAmount($subtotal)) {
            $this->clearDiscount();
            return;
        }

        // Recalculate discount (capped at subtotal - 0.01 to ensure total > 0)
        $newDiscount = $discountCode->calculateDiscount($subtotal);

        // Update database directly to ensure it persists
        DB::table('cart')
            ->where('cart_id', $this->cart_id)
            ->update(['discount_amount' => $newDiscount]);

        $this->discount_amount = $newDiscount;
    }

    /**
     * Clear the discount from the cart
     */
    private function clearDiscount(): void
    {
        DB::table('cart')
            ->where('cart_id', $this->cart_id)
            ->update([
                'discount_code' => null,
                'discount_amount' => 0,
            ]);
        $this->discount_code = null;
        $this->discount_amount = 0;
    }
}
