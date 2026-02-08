<?php

namespace App\Http\Controllers;

use App\Models\DiscountCode;
use Illuminate\Http\Request;

class AdminDiscountController extends Controller
{
    /**
     * Display a listing of discount codes.
     */
    public function index()
    {
        $discountCodes = DiscountCode::orderBy('created_at', 'desc')->get();
        return view('admin.discounts.index', compact('discountCodes'));
    }

    /**
     * Show the form for creating a new discount code.
     */
    public function create()
    {
        return view('admin.discounts.create');
    }

    /**
     * Store a newly created discount code.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discount_code,code',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'max_uses' => 'required|integer|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'applies_to' => 'nullable|string|in:movie,game,merch',
        ]);

        // Ensure at least one discount type is set
        if (empty($validated['discount_percentage']) && empty($validated['discount_amount'])) {
            return back()->withErrors(['discount' => 'Please set either a percentage or fixed amount discount.'])->withInput();
        }

        $discountCode = DiscountCode::create([
            'code' => strtoupper($validated['code']),
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
            'max_uses' => $validated['max_uses'],
            'current_uses' => 0,
            'minimum_purchase' => $validated['minimum_purchase'] ?? 0,
            'applies_to' => $validated['applies_to'] ?? null,
        ]);

        return redirect()->route('admin.discounts.index')
            ->with('success', "Discount code '{$discountCode->code}' created successfully.");
    }

    /**
     * Show the form for editing a discount code.
     */
    public function edit(DiscountCode $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    /**
     * Update the specified discount code.
     */
    public function update(Request $request, DiscountCode $discount)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discount_code,code,' . $discount->code_id . ',code_id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'max_uses' => 'required|integer|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'applies_to' => 'nullable|string|in:movie,game,merch',
        ]);

        // Ensure at least one discount type is set
        if (empty($validated['discount_percentage']) && empty($validated['discount_amount'])) {
            return back()->withErrors(['discount' => 'Please set either a percentage or fixed amount discount.'])->withInput();
        }

        $discount->update([
            'code' => strtoupper($validated['code']),
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
            'max_uses' => $validated['max_uses'],
            'minimum_purchase' => $validated['minimum_purchase'] ?? 0,
            'applies_to' => $validated['applies_to'] ?? null,
        ]);

        return redirect()->route('admin.discounts.index')
            ->with('success', "Discount code '{$discount->code}' updated successfully.");
    }

    /**
     * Remove the specified discount code.
     */
    public function destroy(DiscountCode $discount)
    {
        $code = $discount->code;
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', "Discount code '{$code}' deleted successfully.");
    }
}
