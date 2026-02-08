<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminDiscountController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProductController;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;

Route::get('/', function () {
    return view('homepage');
})->name('home');

// Product routes with filtering
Route::get('/movies', [ProductController::class, 'index'])->defaults('type', 'movie')->name('movies');
Route::get('/games', [ProductController::class, 'index'])->defaults('type', 'game')->name('games');
Route::get('/merch', [ProductController::class, 'index'])->defaults('type', 'merch')->name('merch');

// AJAX filter endpoint
Route::get('/products/{type}/filter', [ProductController::class, 'filter'])->name('products.filter');

Route::get('/about', function () {
    return view('about');
})->name('about');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/account', function () {
        $user = auth()->user();

        // Get orders for this user
        $orders = Order::where('user_id', $user->user_id)
            ->with('items')
            ->orderBy('order_date', 'desc')
            ->get();

        // Calculate stats from real data
        $stats = [
            'total_orders' => $orders->count(),
            'total_spent' => $orders->sum('total_amount'),
        ];

        // Get cart items count
        $cart = Cart::where('user_id', $user->user_id)->with('items')->first();
        $cartItemsCount = $cart ? $cart->items->sum('quantity') : 0;

        return view('user-account', compact('user', 'stats', 'cartItemsCount'));
    })->name('account');

    Route::get('/account/edit', function () {
        $user = auth()->user();
        return view('account-edit', compact('user'));
    })->name('account.edit');

    Route::put('/account/edit', [AuthController::class, 'updateProfile'])->name('account.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/apply-discount', [CheckoutController::class, 'applyDiscount'])->name('checkout.apply-discount');
    Route::post('/checkout/remove-discount', [CheckoutController::class, 'removeDiscount'])->name('checkout.remove-discount');
    Route::get('/checkout/status', [CheckoutController::class, 'status'])->name('checkout.status');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Order routes
    Route::get('/order/confirmation/{order}', [OrderController::class, 'confirmation'])->name('order.confirmation');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Discount code management
    Route::get('/discounts', [AdminDiscountController::class, 'index'])->name('discounts.index');
    Route::get('/discounts/create', [AdminDiscountController::class, 'create'])->name('discounts.create');
    Route::post('/discounts', [AdminDiscountController::class, 'store'])->name('discounts.store');
    Route::get('/discounts/{discount}/edit', [AdminDiscountController::class, 'edit'])->name('discounts.edit');
    Route::put('/discounts/{discount}', [AdminDiscountController::class, 'update'])->name('discounts.update');
    Route::delete('/discounts/{discount}', [AdminDiscountController::class, 'destroy'])->name('discounts.destroy');

    // User management (XSS vulnerability target)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
});
