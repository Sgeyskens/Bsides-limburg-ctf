<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Games - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-black text-white overflow-x-hidden"
      data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">

<!-- Background -->
<div class="fixed inset-0 z-0">
    <img src="{{ asset('images/hero-sky.png') }}"
         alt="Red Sky Background"
         class="absolute inset-0 w-full h-full object-cover object-center">

    <div class="md:hidden absolute inset-0 z-5">
        <img src="{{ asset('images/jason-full.png') }}"
             alt="Jason Voorhees"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0 bg-gradient-to-b from-red-900/50 via-red-900/30 to-black/70"></div>
    </div>

    <img src="{{ asset('images/mountains.png') }}"
         alt="Mountains"
         class="hidden md:block absolute bottom-0 left-0 w-full h-auto z-10">

    <img src="{{ asset('images/jason-full.png') }}"
         alt="Jason Voorhees"
         class="absolute bottom-0 right-0 h-[350px] lg:h-[450px] w-auto object-contain z-20 hidden md:block">

    <img src="{{ asset('images/watchtower.png') }}"
         alt="Watchtower"
         class="absolute bottom-0 right-[180px] lg:right-[300px] h-40 lg:h-70 w-auto object-contain z-30 hidden md:block">
</div>

<div class="relative z-40 min-h-screen flex flex-col">

    <!-- Navigation -->
    <div class="px-4 md:px-8 lg:px-16 py-4 max-w-7xl w-full mx-auto">
        @include('partials.navbar')
    </div>

    <!-- Main -->
    <main class="flex-1 px-4 md:px-8 lg:px-16 py-8 max-w-7xl w-full mx-auto">

        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="friday13-title text-5xl md:text-6xl lg:text-7xl uppercase tracking-wider mb-4">
                GAMES
            </h1>
            <p class="font-cinzel text-base md:text-lg text-[#D8C9AE] max-w-3xl mx-auto">
                Experience the terror in interactive form
            </p>
        </div>

        <!-- Mobile Filter Toggle -->
        <div class="md:hidden mb-4">
            <button id="mobile-filter-toggle"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-sm uppercase rounded border border-red-700 transition-all">
                Filters & Sort
            </button>
        </div>

        <!-- Products Count -->
        <div class="mb-4">
            <p id="products-count" class="font-cinzel text-sm text-gray-400">
                {{ count($games ?? []) }} product{{ count($games ?? []) !== 1 ? 's' : '' }} found
            </p>
        </div>

        <div class="flex flex-col md:flex-row gap-6 mb-16">

            <!-- Filters -->
            <aside id="filter-container" class="hidden md:block md:w-64 lg:w-72 flex-shrink-0">
                @include('partials.product-filters', [
                    'filterOptions' => $filterOptions ?? [],
                    'activeFilters' => $activeFilters ?? [],
                    'productType' => $productType ?? 'game'
                ])
            </aside>

            <!-- Loading -->
            <div id="loading-overlay"
                 class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center">
                <div class="bg-black/90 border-2 border-red-700 rounded-lg p-6">
                    <div class="animate-spin w-8 h-8 border-4 border-red-700 border-t-transparent rounded-full mx-auto"></div>
                    <p class="font-cinzel text-sm text-white mt-3">Loading...</p>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1">

                <!-- Products Grid (SINGLE INSTANCE) -->
                <div id="products-grid"
                     class="grid grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 items-stretch {{ empty($games) ? 'hidden' : '' }}">

                    @foreach($games ?? [] as $game)
                        <div class="flex flex-col h-full bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg overflow-hidden transition-all hover:border-red-700 hover:scale-105 hover:shadow-xl hover:shadow-red-900/30">

                            <div class="aspect-[2/3] bg-gradient-to-br from-red-950/30 to-black flex items-center justify-center border-b-2 border-red-900/50">
                                @if($game->image_url)
                                    <img src="{{ str_starts_with($game->image_url, 'http') ? $game->image_url : asset($game->image_url) }}"
                                         alt="{{ $game->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/mask-logo.png') }}"
                                         alt="Game Poster"
                                         class="w-20 h-20 opacity-60">
                                @endif
                            </div>

                            <div class="flex flex-col flex-grow p-3">
                                <h3 class="font-cinzel text-xs md:text-sm text-[#D8C9AE] mb-1 uppercase tracking-wide line-clamp-2">
                                    {{ $game->name }}
                                </h3>

                                @if($game->description)
                                    <p class="text-xs text-gray-400 mb-2 line-clamp-2">
                                        {{ $game->description }}
                                    </p>
                                @endif

                                <div class="mt-auto">
                                    <span class="text-red-700 font-bold text-lg font-cinzel">
                                        ${{ number_format($game->price, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

                <!-- No Results (SINGLE INSTANCE) -->
                <div id="no-results"
                     class="{{ empty($games) ? '' : 'hidden' }} text-center py-20">
                    <div class="bg-gradient-to-br from-red-950/30 via-black/80 to-red-950/30 border-2 border-red-900/50 rounded-lg p-12 max-w-2xl mx-auto backdrop-blur-sm">
                        <img src="{{ asset('images/mask-logo.png') }}"
                             alt="Jason Mask"
                             class="w-32 h-32 mx-auto mb-6 opacity-60">

                        <p class="font-cinzel text-2xl text-[#D8C9AE] mb-4">
                            {{ empty($games)
                                ? 'No games available yet'
                                : 'No games match your filters' }}
                        </p>

                        <p class="text-gray-400 font-cinzel">
                            {{ empty($games)
                                ? 'Check back soon for the complete Friday the 13th game collection'
                                : 'Try adjusting your filters to find what you’re looking for' }}
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

<!-- Toast -->
<div id="toast"
     class="fixed bottom-4 right-4 bg-black/90 border-2 border-red-700 text-white px-6 py-4 rounded-lg font-cinzel transform translate-y-20 opacity-0 transition-all duration-300 z-50">
    <span id="toast-message"></span>
</div>

</body>
</html>
