<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Manage Discount Codes - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white min-h-screen">
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
                        Users
                    </a>
                    <a href="{{ route('account') }}" class="font-cinzel text-sm text-[#D8C9AE] hover:text-red-700 transition-colors">
                        Back to Account
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 md:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="font-cinzel text-3xl md:text-4xl text-[#D8C9AE] uppercase tracking-wider font-bold">Discount Codes</h2>
                <p class="font-cinzel text-sm text-gray-400 mt-1">Create and manage discount codes for your store</p>
            </div>
            <a href="{{ route('admin.discounts.create') }}" class="inline-flex items-center gap-2 font-cinzel px-6 py-3 bg-red-900 hover:bg-red-800 text-white uppercase tracking-widest text-sm font-bold transition-all hover:scale-105 rounded border border-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Code
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-900/30 border border-green-700 text-green-400 px-6 py-4 rounded mb-6 font-cinzel">
                {{ session('success') }}
            </div>
        @endif

        <!-- Discount Codes Table -->
        <div class="bg-black/80 border-2 border-red-900/50 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-900/30 border-b border-red-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Code</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Discount</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Min. Purchase</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Valid Period</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Usage</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-900/30">
                        @forelse($discountCodes as $code)
                            <tr class="hover:bg-red-900/10 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="font-cinzel text-white font-bold tracking-wider">{{ $code->code }}</span>
                                    @if($code->applies_to)
                                        <span class="block text-xs text-gray-500 font-cinzel mt-1">{{ ucfirst($code->applies_to) }} only</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-cinzel text-red-500">
                                    @if($code->discount_percentage > 0)
                                        {{ $code->discount_percentage }}% OFF
                                    @else
                                        ${{ number_format($code->discount_amount, 2) }} OFF
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-cinzel text-gray-300">
                                    @if($code->minimum_purchase > 0)
                                        ${{ number_format($code->minimum_purchase, 2) }}
                                    @else
                                        <span class="text-gray-500">None</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-cinzel text-gray-300 text-sm">
                                    {{ $code->valid_from->format('M d, Y') }}<br>
                                    <span class="text-gray-500">to</span> {{ $code->valid_until->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-4 font-cinzel text-gray-300">
                                    {{ $code->current_uses }} / {{ $code->max_uses > 0 ? $code->max_uses : 'Unlimited' }}
                                </td>
                                <td class="px-4 py-4">
                                    @if($code->isValid())
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-cinzel uppercase tracking-wider bg-green-900/50 text-green-500 border border-green-700">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-cinzel uppercase tracking-wider bg-gray-900/50 text-gray-500 border border-gray-700">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.discounts.edit', $code) }}" class="p-2 text-[#D8C9AE] hover:text-white hover:bg-red-900/50 rounded transition-colors" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.discounts.destroy', $code) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discount code?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:text-white hover:bg-red-900/50 rounded transition-colors" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="font-cinzel text-gray-400">
                                        <p class="text-lg mb-2">No discount codes found</p>
                                        <p class="text-sm">Create your first discount code to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
