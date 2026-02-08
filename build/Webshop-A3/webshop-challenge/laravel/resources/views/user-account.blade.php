<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>My Account - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden">
    <!-- XSS Detection: Override alert to show flag when XSS is executed -->
    <script>
        (function() {
            const originalAlert = window.alert;
            window.alert = function(msg) {
                originalAlert('FLAG: CTF{STORED_XSS_VULNERABILITY}\n\nYour payload triggered: ' + msg);
            };
        })();
    </script>

    <!-- Hero Background Section - Only at top -->
    <div class="absolute top-0 left-0 right-0 h-screen z-0">
        <!-- Desktop: Red Sky Background -->
        <img src="{{ asset('images/hero-sky.png') }}" alt="Red Sky Background" class="absolute inset-0 w-full h-full object-cover object-center">
        
        <!-- Mobile: Large Jason Background Overlay (on top of red sky) -->
        <div class="md:hidden absolute inset-0 z-5">
            <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="w-full h-full object-cover object-center">
            <!-- Red overlay for mobile to darken Jason image -->
            <div class="absolute inset-0 bg-gradient-to-b from-red-900/50 via-red-900/30 to-black/70"></div>
        </div>
        
        <!-- Mountains Silhouette Layer - Desktop Only -->
        <img src="{{ asset('images/mountains.png') }}" alt="Mountains" class="hidden md:block absolute bottom-0 left-0 w-full h-auto">

        <!-- Watchtower - Desktop Only -->
        <img src="{{ asset('images/watchtower.png') }}" alt="Watchtower" class="absolute bottom-0 right-[50px] lg:right-[150px] h-40 lg:h-70 w-auto object-contain hidden md:block">
        
        <!-- Gradient fade to black at bottom -->
        <div class="absolute bottom-0 left-0 right-0 h-96 bg-gradient-to-b from-transparent to-black"></div>
    </div>

    <!-- Content Wrapper -->
    <div class="relative z-10">
        <div class="px-4 md:px-8 lg:px-16 py-4 max-w-7xl w-full mx-auto">
            @include('partials.navbar')
        </div>

        <!-- Success Message -->
    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 md:px-8 mt-4">
            <div class="bg-green-900/30 border border-green-700 text-green-400 px-6 py-4 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Account Page -->
    <div class="min-h-screen py-12 px-4 md:px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Page Title -->
            <h1 class="font-cinzel text-4xl md:text-5xl lg:text-6xl text-center mb-12 uppercase tracking-wider text-[#D8C9AE]">
                My Account
            </h1>

            <!-- Account Card -->
            <div class="bg-gradient-to-br from-red-950/30 via-black/80 to-red-950/30 border-2 border-red-900/50 rounded-lg overflow-hidden backdrop-blur-sm shadow-2xl shadow-red-900/20">
                
                <!-- Profile Section -->
                <div class="p-8 md:p-12 border-b border-red-900/50">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <!-- Avatar -->
                        <div class="relative group">
                            <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-red-900/50 overflow-hidden bg-black/60 flex items-center justify-center shadow-lg shadow-red-900/30 transition-transform group-hover:scale-105">
                                <img src="{{ $user->avatar_url }}" alt="Profile" class="w-24 h-24 md:w-32 md:h-32 object-contain opacity-80">
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-red-900 text-white text-xs font-bold px-3 py-1 rounded-full border-2 border-black">
                                {{ $user->is_admin ? 'ADMIN' : 'SURVIVOR' }}
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 text-center md:text-left">
                            <h2 class="font-cinzel text-3xl md:text-4xl text-[#D8C9AE] mb-2 uppercase tracking-wide">
                                {{ $user->username }}
                            </h2>
                            <p class="text-red-700 font-cinzel text-lg mb-2">
                                {{ $user->email }}
                            </p>
                            @if($user->bio)
                            <div class="text-gray-400 text-sm mb-4 max-w-md">
                                {{-- VULNERABLE: Using {!! !!} renders raw HTML, allowing XSS --}}
                                {!! $user->bio !!}
                            </div>
                            @endif
                            <a href="{{ route('account.edit') }}" class="inline-block bg-red-900/50 hover:bg-red-900 text-white font-cinzel px-6 py-2 rounded border-2 border-red-700 transition-all hover:scale-105 uppercase text-sm tracking-wider">
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Account Menu -->
                <div class="grid {{ $user->isAdmin() ? 'md:grid-cols-4' : 'md:grid-cols-3' }} border-b border-red-900/50">
                    <a href="#overview" class="flex flex-col items-center gap-2 py-6 px-4 text-[#D8C9AE] font-cinzel text-xs uppercase tracking-wider transition-all border-b-2 border-red-700 bg-red-900/30 hover:bg-red-900/20 hover:border-red-700 hover:text-red-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>OVERVIEW</span>
                    </a>
                    @if($user->isAdmin())
                        <a href="{{ route('admin.discounts.index') }}" class="flex flex-col items-center gap-2 py-6 px-4 text-[#D8C9AE] font-cinzel text-xs uppercase tracking-wider transition-all border-b-2 border-transparent hover:bg-red-900/20 hover:border-red-700 hover:text-red-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>ADMIN PANEL</span>
                        </a>
                    @endif
                    <a href="/" class="flex flex-col items-center gap-2 py-6 px-4 text-[#D8C9AE] font-cinzel text-xs uppercase tracking-wider transition-all border-b-2 border-transparent hover:bg-red-900/20 hover:border-red-700 hover:text-red-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>BACK TO SHOP</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="flex flex-col items-center gap-2 py-6 px-4 text-[#D8C9AE] font-cinzel text-xs uppercase tracking-wider transition-all border-b-2 border-transparent hover:bg-red-900/20 hover:border-red-700 hover:text-red-700 cursor-pointer">
                        @csrf
                        <button type="submit" class="flex flex-col items-center gap-2 w-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>LOGOUT</span>
                        </button>
                    </form>
                </div>

                <!-- Account Stats -->
                <div class="p-4 md:p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                        <!-- Total Orders -->
                        <div class="bg-gradient-to-br from-red-950/20 to-black/40 border border-red-900/30 rounded-lg p-4 md:p-6 text-center transition-all hover:border-red-700/50 hover:shadow-lg hover:shadow-red-900/20 hover:scale-105">
                            <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full flex items-center justify-center text-[#D8C9AE] bg-red-900/30">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div class="text-2xl md:text-4xl font-bold text-red-700 font-cinzel mb-1 md:mb-2">{{ $stats['total_orders'] }}</div>
                            <div class="text-xs text-gray-400 uppercase tracking-wider font-cinzel">TOTAL ORDERS</div>
                        </div>

                        <!-- Total Spent -->
                        <div class="bg-gradient-to-br from-red-950/20 to-black/40 border border-red-900/30 rounded-lg p-4 md:p-6 text-center transition-all hover:border-red-700/50 hover:shadow-lg hover:shadow-red-900/20 hover:scale-105">
                            <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full flex items-center justify-center text-[#D8C9AE] bg-yellow-900/30">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-2xl md:text-4xl font-bold text-red-700 font-cinzel mb-1 md:mb-2">${{ $stats['total_spent'] }}</div>
                            <div class="text-xs text-gray-400 uppercase tracking-wider font-cinzel">TOTAL SPENT</div>
                        </div>

                        <!-- Cart Items -->
                        <div class="bg-gradient-to-br from-red-950/20 to-black/40 border border-red-900/30 rounded-lg p-4 md:p-6 text-center transition-all hover:border-red-700/50 hover:shadow-lg hover:shadow-red-900/20 hover:scale-105">
                            <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full flex items-center justify-center text-[#D8C9AE] bg-red-900/30">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="text-2xl md:text-4xl font-bold text-red-700 font-cinzel mb-1 md:mb-2">{{ $cartItemsCount }}</div>
                            <div class="text-xs text-gray-400 uppercase tracking-wider font-cinzel">CART ITEMS</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('partials.footer')
    </div>
</body>
</html>
