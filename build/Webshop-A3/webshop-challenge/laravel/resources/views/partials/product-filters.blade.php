{{-- Product Filters Sidebar --}}
<div id="filter-sidebar" class="bg-black/90 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-4 md:p-6" data-product-type="{{ $productType }}">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-cinzel text-lg text-[#D8C9AE] uppercase tracking-wide">Filters</h2>
        <div class="flex items-center gap-4">
            <button id="clear-filters" class="text-xs text-red-500 hover:text-red-400 font-cinzel uppercase tracking-wide transition-colors">
                Clear All
            </button>
            <button id="mobile-filter-close" class="md:hidden text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Sort Options --}}
    <div class="mb-6 pb-6 border-b border-red-900/30">
        <h3 class="font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wide mb-3">Sort By</h3>
        <div class="space-y-2">
            <select id="sort-by" class="w-full bg-black/50 border-2 border-red-900/50 rounded px-3 py-2 font-cinzel text-sm text-white focus:border-red-700 focus:outline-none transition-colors">
                <option value="name" {{ ($activeFilters['sort_by'] ?? 'name') === 'name' ? 'selected' : '' }}>Name</option>
                <option value="price" {{ ($activeFilters['sort_by'] ?? '') === 'price' ? 'selected' : '' }}>Price</option>
                <option value="rating" {{ ($activeFilters['sort_by'] ?? '') === 'rating' ? 'selected' : '' }}>Rating</option>
                <option value="newest" {{ ($activeFilters['sort_by'] ?? '') === 'newest' ? 'selected' : '' }}>Newest</option>
            </select>
            <div class="flex gap-2">
                <button id="sort-asc" class="flex-1 px-3 py-1.5 text-xs font-cinzel uppercase rounded border transition-all {{ ($activeFilters['sort_order'] ?? 'asc') === 'asc' ? 'bg-red-900/50 border-red-700 text-white' : 'bg-black/50 border-red-900/50 text-gray-400 hover:border-red-700' }}">
                    Ascending
                </button>
                <button id="sort-desc" class="flex-1 px-3 py-1.5 text-xs font-cinzel uppercase rounded border transition-all {{ ($activeFilters['sort_order'] ?? '') === 'desc' ? 'bg-red-900/50 border-red-700 text-white' : 'bg-black/50 border-red-900/50 text-gray-400 hover:border-red-700' }}">
                    Descending
                </button>
            </div>
        </div>
    </div>

    {{-- Price Filter --}}
    <div class="mb-6 pb-6 border-b border-red-900/30">
        <h3 class="font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wide mb-3">Price Range</h3>
        <div class="space-y-3">
            <div class="flex gap-2 items-center">
                <div class="flex-1">
                    <label for="min-price" class="text-xs text-gray-400 mb-1 block">Min</label>
                    <input type="number" id="min-price" name="min_price"
                        min="{{ $filterOptions['minPrice'] ?? 0 }}"
                        max="{{ $filterOptions['maxPrice'] ?? 1000 }}"
                        step="0.01"
                        value="{{ $activeFilters['min_price'] ?? '' }}"
                        placeholder="${{ number_format($filterOptions['minPrice'] ?? 0, 2) }}"
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-3 py-2 font-cinzel text-sm text-white focus:border-red-700 focus:outline-none transition-colors">
                </div>
                <span class="text-gray-500 mt-5">-</span>
                <div class="flex-1">
                    <label for="max-price" class="text-xs text-gray-400 mb-1 block">Max</label>
                    <input type="number" id="max-price" name="max_price"
                        min="{{ $filterOptions['minPrice'] ?? 0 }}"
                        max="{{ $filterOptions['maxPrice'] ?? 1000 }}"
                        step="0.01"
                        value="{{ $activeFilters['max_price'] ?? '' }}"
                        placeholder="${{ number_format($filterOptions['maxPrice'] ?? 100, 2) }}"
                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-3 py-2 font-cinzel text-sm text-white focus:border-red-700 focus:outline-none transition-colors">
                </div>
            </div>
            <div class="text-xs text-gray-500 font-cinzel">
                Range: ${{ number_format($filterOptions['minPrice'] ?? 0, 2) }} - ${{ number_format($filterOptions['maxPrice'] ?? 100, 2) }}
            </div>
        </div>
    </div>

    {{-- Rating Filter --}}
    <div class="mb-6 pb-6 border-b border-red-900/30">
        <h3 class="font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wide mb-3">Minimum Rating</h3>
        <div class="space-y-2">
            @for($i = 5; $i >= 1; $i--)
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="radio" name="min_rating" value="{{ $i }}"
                        class="accent-red-700 w-4 h-4"
                        {{ ($activeFilters['min_rating'] ?? '') == $i ? 'checked' : '' }}>
                    <span class="flex items-center gap-1">
                        @for($j = 1; $j <= 5; $j++)
                            <svg class="w-4 h-4 {{ $j <= $i ? 'text-red-700' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="text-xs text-gray-400 ml-1 group-hover:text-white transition-colors">& Up</span>
                    </span>
                </label>
            @endfor
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="radio" name="min_rating" value=""
                    class="accent-red-700 w-4 h-4"
                    {{ empty($activeFilters['min_rating']) ? 'checked' : '' }}>
                <span class="text-xs text-gray-400 group-hover:text-white transition-colors">All Ratings</span>
            </label>
        </div>
    </div>

    {{-- Category/Property Filters --}}
    @if(!empty($filterOptions['properties']))
        @foreach($filterOptions['properties'] as $propertyName => $values)
            <div class="mb-6 pb-6 border-b border-red-900/30 last:border-0 last:pb-0 last:mb-0">
                <h3 class="font-cinzel text-sm text-[#D8C9AE] uppercase tracking-wide mb-3">{{ $propertyName }}</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                    @foreach($values as $value)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox"
                                name="properties[{{ $propertyName }}][]"
                                value="{{ $value }}"
                                class="accent-red-700 w-4 h-4 rounded"
                                {{ in_array($value, $activeFilters['properties'][$propertyName] ?? []) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-300 group-hover:text-white transition-colors">{{ $value }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif

    {{-- Apply Button (for mobile) --}}
    <div class="md:hidden mt-6">
        <button id="apply-filters-mobile" class="w-full px-4 py-2 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-sm uppercase rounded border border-red-700 transition-all">
            Apply Filters
        </button>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(127, 29, 29, 0.5);
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(127, 29, 29, 0.8);
    }
</style>
