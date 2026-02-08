<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Search Results - Friday the 13th</title>
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
                    SEARCH RESULTS
                </h1>
                @if($query)
                    <p class="font-cinzel text-base md:text-lg text-[#D8C9AE] max-w-3xl mx-auto">
                        Showing results for "<span class="text-red-500">{{ $query }}</span>"
                        @if($products->count() > 0)
                            - {{ $products->count() }} {{ Str::plural('item', $products->count()) }} found
                        @endif
                    </p>
                @endif
            </div>

            <!-- Search Form -->
            <div class="max-w-2xl mx-auto mb-12">
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Search products..."
                        class="w-full px-6 py-4 bg-black/80 border-2 border-red-900/50 rounded-lg font-cinzel text-white placeholder-gray-500 focus:outline-none focus:border-red-700 transition-colors"
                        autofocus
                    >
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-red-700 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Results Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 mb-16 items-stretch">
                    @foreach($products as $product)
                        <div class="flex flex-col h-full bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg overflow-hidden transition-all hover:border-red-700 hover:scale-105 hover:shadow-xl hover:shadow-red-900/30">
                            <!-- Product Image -->
                            <div class="aspect-[2/3] bg-gradient-to-br from-red-950/30 to-black flex items-center justify-center border-b-2 border-red-900/50">
                                @if($product->image_url)
                                    <img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset($product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/mask-logo.png') }}" alt="Product" class="w-20 h-20 md:w-24 md:h-24 opacity-60">
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex flex-col flex-grow p-3">
                                <!-- Product Type Badge -->
                                <span class="inline-block self-start px-2 py-0.5 text-[10px] uppercase tracking-wider font-cinzel rounded bg-red-900/50 text-red-300 border border-red-700/50 mb-2">
                                    {{ $product->product_type }}
                                </span>

                                <h3 class="font-cinzel text-xs md:text-sm text-[#D8C9AE] mb-1 uppercase tracking-wide line-clamp-2">
                                    {{ $product->name }}
                                </h3>

                                @if($product->description)
                                    <p class="text-xs text-gray-400 mb-2 line-clamp-2">
                                        {{ $product->description }}
                                    </p>
                                @endif

                                <!-- Price & Button -->
                                <div class="flex flex-col gap-2 mt-auto">
                                    <span class="text-red-700 font-bold text-lg font-cinzel">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    @auth
                                        <button data-add-to-cart data-product-id="{{ $product->product_id }}" data-product-type="{{ $product->product_type }}" class="product-card flex items-center justify-center gap-2 px-3 py-1.5 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-xs uppercase rounded border border-red-700 transition-all hover:scale-105">
                                            <img src="{{ asset('favicon.svg') }}" alt="Jason Mask" class="w-4 h-4">
                                            Add to Cart
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 px-3 py-1.5 bg-gray-800/50 hover:bg-gray-700 text-gray-300 font-cinzel text-xs uppercase rounded border border-gray-600 transition-all hover:scale-105">
                                            <img src="{{ asset('favicon.svg') }}" alt="Jason Mask" class="w-4 h-4 opacity-50">
                                            Login to Buy
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-20">
                    <div class="bg-gradient-to-br from-red-950/30 via-black/80 to-red-950/30 border-2 border-red-900/50 rounded-lg p-12 max-w-2xl mx-auto backdrop-blur-sm">
                        <img src="{{ asset('images/mask-logo.png') }}" alt="Jason Mask" class="w-32 h-32 mx-auto mb-6 opacity-60 object-contain">
                        @if($query)
                            <p class="font-cinzel text-2xl text-[#D8C9AE] mb-4">No results found</p>
                            <p class="text-gray-400 font-cinzel mb-6">
                                We couldn't find any products matching "{{ $query }}"
                            </p>
                            <div class="flex flex-wrap justify-center gap-4">
                                <a href="{{ route('movies') }}" class="px-6 py-2 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-sm uppercase rounded border border-red-700 transition-all">
                                    Browse Movies
                                </a>
                                <a href="{{ route('games') }}" class="px-6 py-2 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-sm uppercase rounded border border-red-700 transition-all">
                                    Browse Games
                                </a>
                                <a href="{{ route('merch') }}" class="px-6 py-2 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-sm uppercase rounded border border-red-700 transition-all">
                                    Browse Merch
                                </a>
                            </div>
                        @else
                            <p class="font-cinzel text-2xl text-[#D8C9AE] mb-4">Start searching</p>
                            <p class="text-gray-400 font-cinzel">
                                Enter a search term above to find products
                            </p>
                        @endif
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
