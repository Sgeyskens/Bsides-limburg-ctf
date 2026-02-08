<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Merch - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden" data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">

    <!-- Background Section (Same as Homepage) -->
    <div class="fixed inset-0 z-0">
        <!-- Desktop: Red Sky Background -->
        <img src="{{ asset('images/hero-sky.png') }}" alt="Red Sky Background" class="absolute inset-0 w-full h-full object-cover object-center">

        <!-- Mobile: Large Jason Background Overlay (on top of red sky) -->
        <div class="md:hidden absolute inset-0 z-5">
            <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="w-full h-full object-cover object-center">
            <!-- Red overlay for mobile to darken Jason image -->
            <div class="absolute inset-0 bg-gradient-to-b from-red-900/50 via-red-900/30 to-black/70"></div>
        </div>

        <!-- Mountains Silhouette Layer - Desktop Only -->
        <img src="{{ asset('images/mountains.png') }}" alt="Mountains" class="hidden md:block absolute bottom-0 left-0 w-full h-auto z-10">

        <!-- Jason Full Body - Desktop Only (right side) -->
        <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="absolute bottom-0 right-0 h-[350px] lg:h-[450px] w-auto object-contain z-20 hidden md:block">

        <!-- Watchtower - Desktop Only -->
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
            <div class="text-center mb-8">
                <h1 class="friday13-title text-5xl md:text-6xl lg:text-7xl uppercase tracking-wider mb-4">
                    MERCH
                </h1>
                <p class="font-cinzel text-base md:text-lg text-[#D8C9AE] max-w-3xl mx-auto">
                    Official Friday the 13th merchandise and collectibles
                </p>
            </div>

            <!-- Mobile Filter Toggle -->
            <div class="md:hidden mb-4">
                <button id="mobile-filter-toggle" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-sm uppercase rounded border border-red-700 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters & Sort
                </button>
            </div>

            <!-- Products Count -->
            <div class="mb-4">
                <p id="products-count" class="font-cinzel text-sm text-gray-400">
                    {{ count($merch) }} product{{ count($merch) !== 1 ? 's' : '' }} found
                </p>
            </div>

            <div class="flex flex-col md:flex-row gap-6 mb-16">
                <!-- Filter Sidebar (Desktop: visible, Mobile: hidden by default, shown when toggle clicked) -->
                <aside id="filter-container" class="hidden md:block md:w-64 lg:w-72 flex-shrink-0">
                    @include('partials.product-filters', ['filterOptions' => $filterOptions ?? [], 'activeFilters' => $activeFilters ?? [], 'productType' => $productType ?? 'merch'])
                </aside>

                <!-- Loading Overlay -->
                <div id="loading-overlay" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center">
                    <div class="bg-black/90 border-2 border-red-700 rounded-lg p-6">
                        <div class="animate-spin w-8 h-8 border-4 border-red-700 border-t-transparent rounded-full mx-auto"></div>
                        <p class="font-cinzel text-sm text-white mt-3">Loading...</p>
                    </div>
                </div>

                <!-- Merch Grid -->
                <div class="flex-1">

                    <!-- Products Grid (SINGLE INSTANCE) -->
                    <div id="products-grid"
                         class="grid grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 items-stretch {{ empty($merch) ? 'hidden' : '' }}">

                        @foreach($merch ?? [] as $item)
                            <div class="flex flex-col h-full bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg overflow-hidden transition-all hover:border-red-700 hover:scale-105 hover:shadow-xl hover:shadow-red-900/30">
                                <!-- Merch Image -->
                                <div class="aspect-[2/3] bg-gradient-to-br from-red-950/30 to-black flex items-center justify-center border-b-2 border-red-900/50">
                                    @if($item->image_url)
                                        <img src="{{ str_starts_with($item->image_url, 'http') ? $item->image_url : asset($item->image_url) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/mask-logo.png') }}" alt="Merch" class="w-20 h-20 md:w-24 md:h-24 opacity-60">
                                    @endif
                                </div>

                                <!-- Merch Info -->
                                <div class="flex flex-col flex-grow p-3">
                                    <h3 class="font-cinzel text-xs md:text-sm text-[#D8C9AE] mb-1 uppercase tracking-wide line-clamp-2">
                                        {{ $item->name }}
                                    </h3>

                                    <!-- Description -->
                                    @if($item->description)
                                        <p class="text-xs text-gray-400 mb-2 line-clamp-2">
                                            {{ $item->description }}
                                        </p>
                                    @endif

                                    <!-- Rating -->
                                    @if(($item->rating_count ?? 0) > 0)
                                        <div class="flex items-center gap-1 mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3 h-3 {{ $i <= round($item->avg_rating ?? 0) ? 'text-red-700' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                            <span class="text-xs text-gray-400">({{ $item->rating_count }})</span>
                                        </div>
                                    @endif

                                    <!-- Price & Button -->
                                    <div class="flex flex-col gap-2 mt-auto product-card">
                                        <span class="text-red-700 font-bold text-lg font-cinzel">
                                            ${{ number_format($item->price, 2) }}
                                        </span>
                                        @auth
                                            @if(str_contains($item->name, 'T-Shirt'))
                                                <!-- Size Selector (only for T-Shirts) -->
                                                <select class="size-select w-full bg-black/50 border-2 border-red-900/50 rounded px-2 py-1.5 font-cinzel text-xs text-white focus:border-red-700 focus:outline-none transition-colors">
                                                    <option value="">Select Size</option>
                                                    <option value="S">S</option>
                                                    <option value="M">M</option>
                                                    <option value="L">L</option>
                                                    <option value="XL">XL</option>
                                                    <option value="XXL">XXL</option>
                                                </select>
                                            @endif
                                            <button data-add-to-cart data-product-id="{{ $item->product_id }}" data-product-type="merch" class="flex items-center justify-center gap-2 px-3 py-1.5 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-xs uppercase rounded border border-red-700 transition-all hover:scale-105">
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

                    <!-- No Results (SINGLE INSTANCE) -->
                    <div id="no-results"
                         class="{{ empty($merch) ? '' : 'hidden' }} text-center py-20">
                        <div class="bg-gradient-to-br from-red-950/30 via-black/80 to-red-950/30 border-2 border-red-900/50 rounded-lg p-12 max-w-2xl mx-auto backdrop-blur-sm">
                            <img src="{{ asset('images/mask-logo.png') }}" alt="Jason Mask" class="w-32 h-32 mx-auto mb-6 opacity-60">

                            <p class="font-cinzel text-2xl text-[#D8C9AE] mb-4">
                                {{ empty($merch)
                                    ? 'No merchandise available yet'
                                    : 'No merchandise matches your filters' }}
                            </p>

                            <p class="text-gray-400 font-cinzel">
                                {{ empty($merch)
                                    ? 'Check back soon for official Friday the 13th merchandise'
                                    : 'Try adjusting your filters to find what you\'re looking for' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
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
