<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Login - Friday the 13th</title>
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
        
        <!-- Back to Home Link - Top Left -->
        <a href="{{ route('home') }}" class="absolute top-4 left-4 md:top-6 md:left-8 z-50 inline-flex items-center gap-2 text-[#D8C9AE] font-cinzel text-sm uppercase tracking-wider hover:text-red-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Home
        </a>

        <!-- Content Wrapper -->
        <div class="relative z-40 px-4 md:px-8 lg:px-16 py-4 h-full flex items-center justify-center">

            <!-- Main Content - Login Form -->
            <div class="max-w-lg w-full">
                <!-- Login Title with Friday 13th Font -->
                <h2 class="friday13-title text-4xl md:text-6xl mb-2">
                    LOGIN
                </h2>
                
                <p class="font-cinzel text-sm text-[#D8C9AE] mb-4 uppercase tracking-wider">
                    Welcome back to camp...
                </p>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-3 p-2 bg-red-900/30 border border-red-700 rounded-lg text-red-400 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Login Form Card with Rounded Corners -->
                <div class="relative border-2 border-red-700/80 rounded-lg p-5 md:p-6 bg-black/80">
                    <form method="POST" action="{{ route('login') }}" class="space-y-3">
                        @csrf

                        <!-- Username or Email Field -->
                        <div>
                            <input
                                type="text"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                placeholder="Username or E-mail"
                                class="w-full bg-transparent border border-red-700/80 rounded px-3 py-2 text-white font-cinzel text-sm placeholder-[#D8C9AE] focus:outline-none focus:border-red-500 transition-colors"
                            >
                        </div>

                        <!-- Password Field -->
                        <div>
                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="Password"
                                class="w-full bg-transparent border border-red-700/80 rounded px-3 py-2 text-white font-cinzel text-sm placeholder-[#D8C9AE] focus:outline-none focus:border-red-500 transition-colors"
                            >
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-1 flex justify-center">
                            <button
                                type="submit"
                                class="px-14 py-2 bg-transparent border border-red-700 rounded text-[#D8C9AE] font-cinzel uppercase tracking-[0.3em] text-xs font-bold transition-all hover:bg-red-700 hover:text-white hover:shadow-lg hover:shadow-red-700/40"
                            >
                                Log In
                            </button>
                        </div>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-4 text-center">
                        <p class="text-[#D8C9AE] font-cinzel text-xs">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-red-500 hover:text-red-400 transition-colors">Register</a>
                        </p>
                    </div>
                </div>

                <!-- Chains connecting to sign -->
                <div class="relative h-12 flex justify-center gap-28 -mb-13">
                    <!-- Left Chain -->
                    <img src="{{ asset('images/chain.png') }}" alt="Chain" class="w-4 h-12 object-cover object-top">
                    <!-- Right Chain -->
                    <img src="{{ asset('images/chain.png') }}" alt="Chain" class="w-4 h-12 object-cover object-top">
                </div>

                <!-- Camp Sign -->
                <div class="flex justify-center">
                    <img src="{{ asset('images/camp-sign.png') }}" alt="Camp Crystal Lake Sign" class="w-52 max-w-full">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
