<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Friday the 13th - Camp Crystal Lake</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden">
    <!-- Hero Section with Background Image -->
    <div class="h-screen relative overflow-hidden">
        <!-- Desktop: Red Sky Background -->
        <img src="{{ asset('images/hero-sky.png') }}" alt="Red Sky Background" class="absolute inset-0 w-full h-full object-cover object-center z-0">
        
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
        
        <!-- Content Wrapper -->
        <div class="relative z-40 px-4 md:px-8 lg:px-16 py-4 max-w-7xl w-full mx-auto h-full flex flex-col justify-between">
            @include('partials.navbar')

            <!-- Main Content -->
            <div class="max-w-3xl mb-4">
                <!-- Friday 13th Logo -->
                <img src="{{ asset('images/friday13-logo.png') }}" alt="Friday the 13th" class="w-full max-w-xs md:max-w-sm mb-4">

                <!-- Main Title (Text - Hidden since we're using logo) -->
                <h1 class="text-5xl md:text-7xl lg:text-8xl mb-8 leading-none tracking-wide friday13-title hidden">
                    FRIDAY THE <span class="text-red-700 friday13-title">13<sup>TH</sup></span>
                </h1>

                <!-- Welcome Section -->
                <div class="mb-6">
                    <h2 class="font-cinzel text-xl md:text-2xl lg:text-4xl text-red-700 uppercase tracking-wider mb-1 font-bold leading-tight">
                        WELCOME TO<br>
                        CAMP CRYSTAL LAKE
                    </h2>
                    <p class="font-cinzel text-xs md:text-sm lg:text-base leading-relaxed text-[#D8C9AE] mt-2">
                        The official online store for Friday the 13th.<br>
                        Own the horror with movies, games, and merch<br>
                        of the iconic slasher franchise.
                    </p>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col md:flex-row gap-3 md:gap-4">
                    <a href="{{ route('movies') }}" class="font-cinzel px-6 py-2.5 bg-transparent border-red-700 text-red-700 uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:bg-red-700 hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-red-700/40 text-center cta-button">SHOP MOVIES</a>
                    <a href="{{ route('games') }}" class="font-cinzel px-6 py-2.5 bg-transparent border-red-700 text-red-700 uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:bg-red-700 hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-red-700/40 text-center cta-button">SHOP GAMES</a>
                    <a href="{{ route('merch') }}" class="font-cinzel px-6 py-2.5 bg-transparent border-red-700 text-red-700 uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:bg-red-700 hover:text-white hover:-translate-y-1 hover:shadow-lg hover:shadow-red-700/40 text-center cta-button">SHOP MERCH</a>
                </div>
            </div>

            <!-- Subtle Promo Banner - Bottom of hero -->
            <div class="mb-2">
                <p class="font-cinzel text-xs md:text-sm text-gray-400/80">
                    Use code <span class="text-red-600/90 font-semibold">FRIDAY13</span> for 13% off
                    <span class="hidden md:inline text-gray-600 mx-1">·</span>
                    <span class="hidden md:inline"><span class="text-red-600/90 font-semibold">WELCOME15</span> for 15% off</span>
                </p>
            </div>
        </div>
    </div>

    @include('partials.footer')
</body>
</html>