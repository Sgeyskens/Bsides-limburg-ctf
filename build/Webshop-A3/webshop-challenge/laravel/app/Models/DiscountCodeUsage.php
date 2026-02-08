<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCodeUsage extends Model
{
    protected $table = 'discount_code_usage';

    protected $fillable = [
        'user_id',
        'code_id',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class, 'code_id', 'code_id');
    }

    /**
     * Get the claim for a specific user and discount code
     */
    public static function getUserCodeClaim(int $userId, int $codeId): ?self
    {
        return self::where('code_id', $codeId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Check if a discount code is already used by the current user
     */
    public static function isUsedByUser(int $codeId, int $userId): bool
    {
        return self::getUserCodeClaim($userId, $codeId) !== null;
    }

    /**
     * Try to claim a discount code for a user
     * Each user can use a code once, but different users can all use the same code
     * Returns: 'success' | 'already_claimed_by_user'
     */
    public static function tryClaimCode(int $userId, int $codeId): string
    {
        // Check if this user already claimed this code
        if (self::isUsedByUser($codeId, $userId)) {
            return 'already_claimed_by_user';
        }

        // Try to claim - atomic insert
        try {
            self::create([
                'user_id' => $userId,
                'code_id' => $codeId,
                'applied_at' => now(),
            ]);
            return 'success';
        } catch (\Illuminate\Database\QueryException $e) {
            // Unique constraint violation - user already has this code
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                return 'already_claimed_by_user';
            }
            throw $e;
        }
    }

    /**
     * Release a claim on a discount code (when removing from cart)
     */
    public static function releaseClaim(int $codeId, int $userId): bool
    {
        return self::where('code_id', $codeId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
