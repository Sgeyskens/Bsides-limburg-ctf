<?php

namespace Tests\Unit;

use App\Models\DiscountCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_discount_code_can_be_created(): void
    {
        $code = DiscountCode::factory()->create([
            'code' => 'FRIDAY13',
            'discount_percentage' => 15,
        ]);

        $this->assertDatabaseHas('discount_code', [
            'code' => 'FRIDAY13',
            'discount_percentage' => 15,
        ]);
    }

    public function test_valid_discount_code_is_valid(): void
    {
        $code = DiscountCode::factory()->create();

        $this->assertTrue($code->isValid());
    }

    public function test_expired_discount_code_is_invalid(): void
    {
        $code = DiscountCode::factory()->expired()->create();

        $this->assertFalse($code->isValid());
    }

    public function test_future_discount_code_is_invalid(): void
    {
        $code = DiscountCode::factory()->future()->create();

        $this->assertFalse($code->isValid());
    }

    public function test_exhausted_discount_code_is_invalid(): void
    {
        $code = DiscountCode::factory()->exhausted()->create();

        $this->assertFalse($code->isValid());
    }

    public function test_percentage_discount_calculation(): void
    {
        $code = DiscountCode::factory()->percentage(10)->create();

        $discount = $code->calculateDiscount(100.00);

        $this->assertEquals(10.00, $discount);
    }

    public function test_fixed_amount_discount_calculation(): void
    {
        $code = DiscountCode::factory()->fixedAmount(15.00)->create();

        $discount = $code->calculateDiscount(100.00);

        $this->assertEquals(15.00, $discount);
    }

    public function test_discount_cannot_exceed_subtotal_minus_one_cent(): void
    {
        $code = DiscountCode::factory()->percentage(100)->create();

        $discount = $code->calculateDiscount(50.00);

        // Should cap at 49.99 (leaving 0.01)
        $this->assertEquals(49.99, $discount);
    }

    public function test_fixed_discount_cannot_exceed_subtotal_minus_one_cent(): void
    {
        $code = DiscountCode::factory()->fixedAmount(100.00)->create();

        $discount = $code->calculateDiscount(50.00);

        // Should cap at 49.99
        $this->assertEquals(49.99, $discount);
    }

    public function test_minimum_purchase_requirement_met(): void
    {
        $code = DiscountCode::factory()->minimumPurchase(50.00)->create();

        $this->assertTrue($code->canApplyToAmount(50.00));
        $this->assertTrue($code->canApplyToAmount(100.00));
    }

    public function test_minimum_purchase_requirement_not_met(): void
    {
        $code = DiscountCode::factory()->minimumPurchase(50.00)->create();

        $this->assertFalse($code->canApplyToAmount(49.99));
        $this->assertFalse($code->canApplyToAmount(25.00));
    }

    public function test_increment_usage(): void
    {
        $code = DiscountCode::factory()->create(['current_uses' => 5]);

        $code->incrementUsage();

        $this->assertEquals(6, $code->fresh()->current_uses);
    }
}
