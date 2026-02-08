<!-- Navigation -->
<nav class="flex items-center justify-between mb-4 md:mb-4">
    <!-- Mobile: Logo and Hamburger Menu -->
    <div class="md:hidden flex items-center justify-between w-full">
        <!-- Logo -->
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/mask-logo.png') }}" alt="Jason Mask" class="w-12 h-12 object-contain hover:scale-110 transition-transform">
        </a>
        <!-- Hamburger Button -->
        <button id="hamburger-btn" class="text-white focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Desktop: Regular Navigation -->
    <div class="hidden md:flex flex-col lg:flex-row items-start lg:items-center gap-6 lg:gap-6 w-full lg:w-auto">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/mask-logo.png') }}" alt="Jason Mask" class="w-14 h-14 lg:w-20 lg:h-20 object-contain hover:scale-110 transition-transform">
        </a>
        <ul class="flex flex-wrap gap-4 lg:gap-8 list-none">
            <li><a href="{{ route('movies') }}" class="text-[#D8C9AE] font-cinzel no-underline text-sm lg:text-base font-bold uppercase tracking-wider hover:text-red-700 transition-colors">MOVIES</a></li>
            <li><a href="{{ route('games') }}" class="text-[#D8C9AE] font-cinzel no-underline text-sm lg:text-base font-bold uppercase tracking-wider hover:text-red-700 transition-colors">GAMES</a></li>
            <li><a href="{{ route('merch') }}" class="text-[#D8C9AE] font-cinzel no-underline text-sm lg:text-base font-bold uppercase tracking-wider hover:text-red-700 transition-colors">MERCH</a></li>
            <li><a href="{{ route('about') }}" class="text-[#D8C9AE] font-cinzel no-underline text-sm lg:text-base font-bold uppercase tracking-wider hover:text-red-700 transition-colors">ABOUT</a></li>
        </ul>
    </div>

    <!-- Desktop: Auth & Cart & Search -->
    <div class="hidden md:flex items-center gap-4">
        <!-- Expandable Search -->
        <div class="relative flex items-center" id="search-container">
            <button id="search-toggle" class="text-[#D8C9AE] hover:text-red-700 transition-colors p-2" title="Search">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            <form action="{{ route('search') }}" method="GET" id="search-form" class="overflow-hidden transition-all duration-300 w-0 opacity-0">
                <input
                    type="text"
                    name="q"
                    id="search-input"
                    placeholder="Search..."
                    autocomplete="off"
                    class="w-48 lg:w-56 px-4 py-2 bg-black/80 border border-red-900/50 rounded-lg font-cinzel text-sm text-white placeholder-gray-500 focus:outline-none focus:border-red-700"
                >
            </form>
            <!-- Autocomplete Dropdown -->
            <div id="search-autocomplete" class="absolute top-full right-0 w-72 mt-2 bg-black/95 border border-red-900/50 rounded-lg overflow-hidden z-50 hidden">
            </div>
        </div>

        @guest
            <a href="{{ route('login') }}" class="hover:scale-110 transition-transform">
                <img src="{{ asset('images/login-text.png') }}" alt="Login" class="h-7 lg:h-9 brightness-110 saturate-150">
            </a>
        @else
            <!-- Cart Link -->
            <a href="{{ route('cart.index') }}" class="cart-link relative text-[#D8C9AE] hover:text-red-700 transition-colors" title="Cart">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="cart-badge absolute -top-2 -right-3 bg-red-700 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
            </a>
            <!-- My Account Link -->
            <a href="{{ route('account') }}" class="text-[#D8C9AE] hover:text-red-700 transition-colors" title="My Account">
                <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </a>
            <!-- Logout Button -->
            <form action="{{ route('logout') }}" method="POST" class="flex items-center">
                @csrf
                <button type="submit" class="text-[#D8C9AE] hover:text-red-700 transition-colors" title="Logout">
                    <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        @endguest
    </div>
</nav>

<!-- Mobile Menu (Hidden by default) -->
<div id="mobile-menu" class="hidden md:hidden absolute top-20 left-0 right-0 bg-black/75 backdrop-blur-sm border-t-2 border-red-700 z-50">
    <ul class="flex flex-col items-center py-8 gap-6">
        <!-- Mobile Search -->
        <li class="w-3/4">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <input
                    type="text"
                    name="q"
                    placeholder="Search products..."
                    class="w-full px-4 py-3 bg-black/60 border border-red-900/50 rounded-lg font-cinzel text-white placeholder-gray-500 focus:outline-none focus:border-red-700 transition-colors"
                >
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-red-700 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </li>
        <li><a href="{{ route('movies') }}" class="mobile-menu-link text-[#D8C9AE] font-cinzel text-lg font-bold uppercase tracking-wider hover:text-red-700 transition-colors">MOVIES</a></li>
        <li><a href="{{ route('games') }}" class="mobile-menu-link text-[#D8C9AE] font-cinzel text-lg font-bold uppercase tracking-wider hover:text-red-700 transition-colors">GAMES</a></li>
        <li><a href="{{ route('merch') }}" class="mobile-menu-link text-[#D8C9AE] font-cinzel text-lg font-bold uppercase tracking-wider hover:text-red-700 transition-colors">MERCH</a></li>
        <li><a href="{{ route('about') }}" class="mobile-menu-link text-[#D8C9AE] font-cinzel text-lg font-bold uppercase tracking-wider hover:text-red-700 transition-colors">ABOUT</a></li>
        <li class="pt-4 border-t border-red-700/50 w-3/4 text-center">
            @guest
                <a href="{{ route('login') }}" class="mobile-menu-link hover:scale-110 transition-transform">
                    <img src="{{ asset('images/login-text.png') }}" alt="Login" class="h-8 inline-block brightness-110 saturate-150">
                </a>
            @else
                <a href="{{ route('cart.index') }}" class="cart-link mobile-menu-link text-[#D8C9AE] hover:text-red-700 transition-colors relative inline-flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="font-cinzel text-lg font-bold uppercase tracking-wider">Cart</span>
                    <span class="cart-badge absolute -top-1 -right-4 bg-red-700 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                </a>
            </li>
            <li class="w-3/4 text-center">
                <a href="{{ route('account') }}" class="mobile-menu-link text-[#D8C9AE] font-cinzel text-lg font-bold uppercase tracking-wider hover:text-red-700 transition-colors">MY ACCOUNT</a>
            </li>
            <li class="w-3/4 text-center">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="mobile-menu-link text-[#D8C9AE] font-cinzel text-lg font-bold uppercase tracking-wider hover:text-red-700 transition-colors">LOGOUT</button>
                </form>
            @endguest
        </li>
    </ul>
</div>
