<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Edit Discount Code - Admin</title>
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
                <a href="{{ route('admin.discounts.index') }}" class="font-cinzel text-sm text-[#D8C9AE] hover:text-red-700 transition-colors">
                    Back to Discount Codes
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 md:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="font-cinzel text-3xl md:text-4xl text-[#D8C9AE] uppercase tracking-wider font-bold">Edit Discount Code</h2>
            <p class="font-cinzel text-sm text-gray-400 mt-1">Modify the discount code: <span class="text-red-500">{{ $discount->code }}</span></p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-700 text-red-400 px-6 py-4 rounded mb-6 font-cinzel">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Usage Stats -->
        <div class="bg-red-900/20 border border-red-900/50 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <span class="font-cinzel text-xs text-gray-400 uppercase tracking-wider">Current Usage</span>
                    <p class="font-cinzel text-xl text-white">{{ $discount->current_uses }} <span class="text-sm text-gray-400">/ {{ $discount->max_uses > 0 ? $discount->max_uses : 'Unlimited' }}</span></p>
                </div>
                <div class="flex-1">
                    <span class="font-cinzel text-xs text-gray-400 uppercase tracking-wider">Status</span>
                    <p class="font-cinzel text-xl">
                        @if($discount->isValid())
                            <span class="text-green-500">Active</span>
                        @else
                            <span class="text-gray-500">Inactive</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" class="bg-black/80 border-2 border-red-900/50 rounded-lg p-6 md:p-8">
            @csrf
            @method('PUT')

            <!-- Code -->
            <div class="mb-6">
                <label for="code" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                    Discount Code <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="code"
                    id="code"
                    value="{{ old('code', $discount->code) }}"
                    required
                    class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors uppercase"
                    placeholder="e.g., FRIDAY13"
                >
                <p class="text-gray-500 text-xs font-cinzel mt-1">Code will be converted to uppercase automatically</p>
            </div>

            <!-- Discount Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="discount_percentage" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                        Percentage Discount
                    </label>
                    <input
                        type="number"
                        name="discount_percentage"
                        id="discount_percentage"
                        value="{{ old('discount_percentage', $discount->discount_percentage) }}"
                        min="0"
                        max="100"
                        step="any"
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                        placeholder="0"
                    >
                </div>
                <div>
                    <label for="discount_amount" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                        Fixed Amount Discount
                    </label>
                    <input
                        type="number"
                        name="discount_amount"
                        id="discount_amount"
                        value="{{ old('discount_amount', $discount->discount_amount) }}"
                        min="0"
                        step="any"
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                        placeholder="0.00"
                    >
                </div>
            </div>
            <p class="text-gray-500 text-xs font-cinzel mb-6">Set either a percentage OR a fixed amount discount (percentage takes priority)</p>

            <!-- Validity Period -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="valid_from" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                        Valid From <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="valid_from"
                        id="valid_from"
                        value="{{ old('valid_from', $discount->valid_from->format('Y-m-d')) }}"
                        required
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white focus:border-red-700 focus:outline-none transition-colors"
                    >
                </div>
                <div>
                    <label for="valid_until" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                        Valid Until <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="valid_until"
                        id="valid_until"
                        value="{{ old('valid_until', $discount->valid_until->format('Y-m-d')) }}"
                        required
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white focus:border-red-700 focus:outline-none transition-colors"
                    >
                </div>
            </div>

            <!-- Usage & Restrictions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="max_uses" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                        Maximum Uses <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="max_uses"
                        id="max_uses"
                        value="{{ old('max_uses', $discount->max_uses) }}"
                        min="0"
                        required
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                        placeholder="0"
                    >
                    <p class="text-gray-500 text-xs font-cinzel mt-1">Set to 0 for unlimited uses</p>
                </div>
                <div>
                    <label for="minimum_purchase" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                        Minimum Purchase
                    </label>
                    <input
                        type="number"
                        name="minimum_purchase"
                        id="minimum_purchase"
                        value="{{ old('minimum_purchase', $discount->minimum_purchase) }}"
                        min="0"
                        step="any"
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                        placeholder="0.00"
                    >
                    <p class="text-gray-500 text-xs font-cinzel mt-1">Minimum cart total required to use this code</p>
                </div>
            </div>

            <!-- Product Restriction -->
            <div class="mb-8">
                <label for="applies_to" class="block font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wider mb-2">
                    Applies To
                </label>
                <select
                    name="applies_to"
                    id="applies_to"
                    class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white focus:border-red-700 focus:outline-none transition-colors"
                >
                    <option value="" {{ old('applies_to', $discount->applies_to) === null ? 'selected' : '' }}>All Products</option>
                    <option value="movie" {{ old('applies_to', $discount->applies_to) === 'movie' ? 'selected' : '' }}>Movies Only</option>
                    <option value="game" {{ old('applies_to', $discount->applies_to) === 'game' ? 'selected' : '' }}>Games Only</option>
                    <option value="merch" {{ old('applies_to', $discount->applies_to) === 'merch' ? 'selected' : '' }}>Merch Only</option>
                </select>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" class="flex-1 font-cinzel px-6 py-3 bg-red-900 hover:bg-red-800 text-white uppercase tracking-widest text-sm font-bold transition-all hover:scale-105 rounded border border-red-700">
                    Update Discount Code
                </button>
                <a href="{{ route('admin.discounts.index') }}" class="flex-1 text-center font-cinzel px-6 py-3 bg-transparent hover:bg-red-900/30 text-[#D8C9AE] uppercase tracking-widest text-sm font-bold transition-all rounded border border-red-900/50">
                    Cancel
                </a>
            </div>
        </form>

        <!-- Danger Zone -->
        <div class="mt-8 bg-red-950/30 border-2 border-red-900/50 rounded-lg p-6">
            <h3 class="font-cinzel text-lg text-red-500 uppercase tracking-wider font-bold mb-4">Danger Zone</h3>
            <p class="font-cinzel text-sm text-gray-400 mb-4">Permanently delete this discount code. This action cannot be undone.</p>
            <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discount code? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="font-cinzel px-6 py-3 bg-red-900/50 hover:bg-red-900 text-white uppercase tracking-widest text-sm font-bold transition-all rounded border border-red-700">
                    Delete Discount Code
                </button>
            </form>
        </div>
    </main>
</body>
</html>
