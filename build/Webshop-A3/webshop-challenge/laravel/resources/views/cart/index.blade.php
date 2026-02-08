<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Your Cart - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden">

    <!-- Background Section -->
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('images/hero-sky.png') }}" alt="Red Sky Background" class="absolute inset-0 w-full h-full object-cover object-center">
        <div class="md:hidden absolute inset-0 z-5">
            <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="w-full h-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-b from-red-900/50 via-red-900/30 to-black/70"></div>
        </div>
        <img src="{{ asset('images/mountains.png') }}" alt="Mountains" class="hidden md:block absolute bottom-0 left-0 w-full h-auto z-10">
        <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="absolute bottom-0 right-0 h-[350px] lg:h-[450px] w-auto object-contain z-20 hidden md:block">
        <img src="{{ asset('images/watchtower.png') }}" alt="Watchtower" class="absolute bottom-0 right-[180px] lg:right-[300px] h-40 lg:h-70 w-auto object-contain z-30 hidden md:block">
    </div>

    <!-- Content Wrapper -->
    <div class="relative z-40 min-h-screen flex flex-col">
        <!-- Navigation -->
        <div class="px-4 md:px-8 lg:px-16 py-4 max-w-7xl w-full mx-auto">
            @include('partials.navbar')
        </div>

        <!-- Main Content -->
        <main class="flex-1 px-4 md:px-8 lg:px-16 py-8 max-w-7xl w-full mx-auto">

            <!-- Page Title -->
            <div class="text-center mb-12">
                <h1 class="friday13-title text-5xl md:text-6xl lg:text-7xl uppercase tracking-wider mb-4">
                    YOUR CART
                </h1>
            </div>

            <!-- Flash Messages -->
            @if(session('error'))
                <div class="bg-red-900/50 border border-red-700 text-white px-4 py-3 rounded mb-6 font-cinzel">
                    {{ session('error') }}
                </div>
            @endif

            @if($cart->items->isEmpty())
                <!-- Empty Cart -->
                <div class="text-center py-20">
                    <div class="bg-gradient-to-br from-red-950/30 via-black/80 to-red-950/30 border-2 border-red-900/50 rounded-lg p-12 max-w-2xl mx-auto backdrop-blur-sm">
                        <img src="{{ asset('images/mask-logo.png') }}" alt="Jason Mask" class="w-32 h-32 mx-auto mb-6 opacity-60 object-contain">
                        <p class="font-cinzel text-2xl text-[#D8C9AE] mb-4">Your cart is empty</p>
                        <p class="text-gray-400 font-cinzel mb-8">
                            Looks like you haven't added any items yet
                        </p>
                        <a href="{{ route('movies') }}" class="inline-block font-cinzel px-6 py-2.5 bg-red-900/50 hover:bg-red-900 text-white uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:scale-105 rounded border border-red-700">
                            Start Shopping
                        </a>
                    </div>
                </div>
            @else
                <!-- Cart Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4" id="cart-items">
                        @foreach($cart->items as $item)
                            @if($item->product)
                                <div class="cart-item bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-4 flex gap-4" data-item-id="{{ $item->cart_item_id }}">
                                    <!-- Product Image -->
                                    <div class="w-24 h-32 flex-shrink-0 bg-gradient-to-br from-red-950/30 to-black rounded border border-red-900/50 overflow-hidden">
                                        @if($item->product->image_url)
                                            <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset($item->product->image_url) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <img src="{{ asset('images/mask-logo.png') }}" alt="Product" class="w-12 h-12 opacity-60 object-contain">
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-1 flex flex-col">
                                        <h3 class="font-cinzel text-sm md:text-base text-[#D8C9AE] uppercase tracking-wide mb-1">
                                            {{ $item->product->name }}
                                        </h3>
                                        @if($item->size)
                                            <span class="text-xs text-gray-400 font-cinzel">Size: {{ $item->size }}</span>
                                        @endif
                                        <span class="text-red-700 font-bold font-cinzel mt-auto">
                                            ${{ number_format($item->product->price, 2) }}
                                        </span>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex flex-col items-end justify-between">
                                        <button type="button" class="remove-item text-gray-400 hover:text-red-700 transition-colors" data-item-id="{{ $item->cart_item_id }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>

                                        <div class="flex items-center gap-2">
                                            <button type="button" class="quantity-btn decrease w-8 h-8 flex items-center justify-center bg-red-900/50 hover:bg-red-900 text-white rounded border border-red-700 transition-all" data-item-id="{{ $item->cart_item_id }}">
                                                -
                                            </button>
                                            <span class="item-quantity w-8 text-center font-cinzel text-white">{{ $item->quantity }}</span>
                                            <button type="button" class="quantity-btn increase w-8 h-8 flex items-center justify-center bg-red-900/50 hover:bg-red-900 text-white rounded border border-red-700 transition-all" data-item-id="{{ $item->cart_item_id }}">
                                                +
                                            </button>
                                        </div>

                                        <span class="item-total text-[#D8C9AE] font-cinzel font-bold">
                                            ${{ number_format($item->product->price * $item->quantity, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 sticky top-4">
                            <h2 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider mb-6 border-b border-red-900/50 pb-4">
                                Order Summary
                            </h2>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between font-cinzel text-gray-300">
                                    <span>Subtotal</span>
                                    <span id="cart-subtotal">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between font-cinzel text-gray-300">
                                    <span>Shipping</span>
                                    <span class="text-green-500">FREE</span>
                                </div>
                            </div>

                            <div class="border-t border-red-900/50 pt-4 mb-6">
                                <div class="flex justify-between font-cinzel text-lg text-[#D8C9AE] font-bold">
                                    <span>Total</span>
                                    <span id="cart-total">${{ number_format($subtotal, 2) }}</span>
                                </div>
                            </div>

                            <a href="{{ route('checkout.index') }}" class="block w-full text-center font-cinzel px-6 py-3 bg-red-900 hover:bg-red-800 text-white uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:scale-105 rounded border border-red-700">
                                Proceed to Checkout
                            </a>

                            <a href="{{ route('movies') }}" class="block w-full text-center font-cinzel px-6 py-3 mt-4 bg-transparent hover:bg-red-900/30 text-[#D8C9AE] uppercase tracking-widest text-xs font-bold cursor-pointer transition-all rounded border border-red-900/50">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <div class="relative z-50">
        @include('partials.footer')
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 bg-black/90 border-2 border-red-700 text-white px-6 py-4 rounded-lg font-cinzel transform translate-y-20 opacity-0 transition-all duration-300 z-50">
        <span id="toast-message"></span>
    </div>
</body>
</html>
