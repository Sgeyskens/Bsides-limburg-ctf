<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>User Profile: {{ $user->username }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white min-h-screen">
    <!-- XSS Detection: Override alert to show flag when XSS is executed -->
    <script>
        (function() {
            const originalAlert = window.alert;
            window.alert = function(msg) {
                originalAlert('FLAG: CTF{STORED_XSS_ADMIN_PWNED}\n\nYour payload triggered: ' + msg);
            };
        })();
    </script>

    <!-- Header -->
    <div class="bg-gradient-to-b from-red-900/30 to-black border-b border-red-900/50">
        <div class="max-w-7xl mx-auto px-4 md:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/mask-logo.png') }}" alt="Jason Mask" class="w-12 h-12 object-contain hover:scale-110 transition-transform">
                    </a>
                    <h1 class="font-cinzel text-xl md:text-2xl text-red-700 uppercase tracking-wider font-bold">Admin Panel</h1>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.users.index') }}" class="font-cinzel text-sm text-gray-400 hover:text-red-700 transition-colors">
                        All Users
                    </a>
                    <a href="{{ route('account') }}" class="font-cinzel text-sm text-[#D8C9AE] hover:text-red-700 transition-colors">
                        Back to Account
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 md:px-8 py-8">
        <!-- Back Link -->
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 font-cinzel text-sm text-gray-400 hover:text-red-700 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Users
        </a>

        <!-- User Profile Card -->
        <div class="bg-black/80 border-2 border-red-900/50 rounded-lg overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-red-900/30 to-black/80 p-8 border-b border-red-900/50">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <!-- Avatar -->
                    <div class="relative">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="w-32 h-32 rounded-full object-cover border-4 border-red-900/50 shadow-lg shadow-red-900/30">
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
                        <p class="text-gray-500 font-cinzel text-sm">
                            Member since {{ $user->created_at->format('F j, Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bio Section - VULNERABLE: Renders raw HTML without sanitization -->
            <div class="p-8">
                <h3 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider mb-4 border-b border-red-900/30 pb-2">
                    User Bio
                </h3>
                @if($user->bio)
                    <div class="text-gray-300 leading-relaxed">
                        {{-- VULNERABLE: Using {!! !!} renders raw HTML, allowing XSS --}}
                        {!! $user->bio !!}
                    </div>
                @else
                    <p class="text-gray-500 italic font-cinzel">This user hasn't written a bio yet.</p>
                @endif
            </div>

            <!-- User Stats -->
            <div class="p-8 bg-red-900/10 border-t border-red-900/50">
                <h3 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider mb-4">
                    Account Statistics
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-black/40 rounded border border-red-900/30">
                        <div class="text-2xl font-bold text-red-700 font-cinzel">{{ $user->orders->count() }}</div>
                        <div class="text-xs text-gray-500 uppercase font-cinzel">Orders</div>
                    </div>
                    <div class="text-center p-4 bg-black/40 rounded border border-red-900/30">
                        <div class="text-2xl font-bold text-red-700 font-cinzel">${{ number_format($user->orders->sum('total_amount'), 2) }}</div>
                        <div class="text-xs text-gray-500 uppercase font-cinzel">Total Spent</div>
                    </div>
                    <div class="text-center p-4 bg-black/40 rounded border border-red-900/30">
                        <div class="text-2xl font-bold text-red-700 font-cinzel">{{ $user->cart ? $user->cart->items->count() : 0 }}</div>
                        <div class="text-xs text-gray-500 uppercase font-cinzel">Cart Items</div>
                    </div>
                    <div class="text-center p-4 bg-black/40 rounded border border-red-900/30">
                        <div class="text-2xl font-bold text-red-700 font-cinzel">{{ $user->is_admin ? 'Yes' : 'No' }}</div>
                        <div class="text-xs text-gray-500 uppercase font-cinzel">Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
