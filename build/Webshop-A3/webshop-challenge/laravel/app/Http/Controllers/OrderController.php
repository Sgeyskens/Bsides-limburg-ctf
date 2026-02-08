<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display the order confirmation page
     */
    public function confirmation(Order $order)
    {
        // Verify the order belongs to the current user
        if ($order->user_id !== Auth::user()->user_id) {
            abort(403, 'You do not have access to this order');
        }

        $order->load('items');

        return view('order.confirmation', compact('order'));
    }
}
