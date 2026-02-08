<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Order Confirmed - Friday the 13th</title>
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

            <!-- Success Message -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-900/50 rounded-full border-2 border-green-500 mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="friday13-title text-4xl md:text-5xl lg:text-6xl uppercase tracking-wider mb-4">
                    ORDER CONFIRMED
                </h1>
                <p class="font-cinzel text-lg text-[#D8C9AE]">
                    Thank you for your purchase!
                </p>
            </div>

            <!-- Order Details -->
            <div class="max-w-3xl mx-auto">
                <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-6 border-b border-red-900/50">
                        <div>
                            <h2 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider">
                                Order {{ $order->order_number }}
                            </h2>
                            <p class="font-cinzel text-sm text-gray-400 mt-1">
                                Placed on {{ $order->order_date->format('F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <span class="mt-4 md:mt-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-cinzel uppercase tracking-wider
                            @if($order->status === 'processing') bg-yellow-900/50 text-yellow-500 border border-yellow-700
                            @elseif($order->status === 'shipped') bg-blue-900/50 text-blue-500 border border-blue-700
                            @elseif($order->status === 'delivered') bg-green-900/50 text-green-500 border border-green-700
                            @else bg-gray-900/50 text-gray-500 border border-gray-700
                            @endif
                        ">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <!-- Order Items -->
                    <h3 class="font-cinzel text-lg text-[#D8C9AE] uppercase tracking-wider mb-4">Items</h3>
                    <div class="space-y-4 mb-6">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center py-3 border-b border-red-900/30">
                                <div class="flex-1">
                                    <span class="font-cinzel text-white">{{ $item->name }}</span>
                                    <span class="font-cinzel text-sm text-gray-400 block">
                                        Qty: {{ $item->quantity }}
                                        @if($item->size) | Size: {{ $item->size }} @endif
                                    </span>
                                </div>
                                <span class="font-cinzel text-[#D8C9AE]">${{ number_format($item->price_per_unit * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Total -->
                    <div class="border-t border-red-900/50 pt-4">
                        @if($order->discount_code)
                            <div class="flex justify-between font-cinzel text-sm text-green-500 mb-2">
                                <span>Discount ({{ $order->discount_code }})</span>
                                <span>Applied</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-cinzel text-lg text-[#D8C9AE] font-bold">
                            <span>Total</span>
                            <span>${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6">
                        <h3 class="font-cinzel text-lg text-[#D8C9AE] uppercase tracking-wider mb-4">Shipping Address</h3>
                        <p class="font-cinzel text-gray-300 whitespace-pre-line">{{ $order->shipping_address }}</p>
                    </div>
                    <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6">
                        <h3 class="font-cinzel text-lg text-[#D8C9AE] uppercase tracking-wider mb-4">Billing Address</h3>
                        <p class="font-cinzel text-gray-300 whitespace-pre-line">{{ $order->billing_address }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('account') }}" class="inline-block text-center font-cinzel px-6 py-3 bg-red-900 hover:bg-red-800 text-white uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:scale-105 rounded border border-red-700">
                        View Order History
                    </a>
                    <a href="{{ route('movies') }}" class="inline-block text-center font-cinzel px-6 py-3 bg-transparent hover:bg-red-900/30 text-[#D8C9AE] uppercase tracking-widest text-sm font-bold cursor-pointer transition-all rounded border border-red-900/50">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </main>
    </div>

    <div class="relative z-50">
        @include('partials.footer')
    </div>
</body>
</html>
