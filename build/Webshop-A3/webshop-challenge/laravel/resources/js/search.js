/**
 * Search - Inline Expanding Search Bar
 */

document.addEventListener('DOMContentLoaded', function () {
    const searchToggle = document.getElementById('search-toggle');
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const autocompleteContainer = document.getElementById('search-autocomplete');

    let isSearchOpen = false;

    // Toggle search bar
    if (searchToggle && searchForm) {
        searchToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (isSearchOpen) {
                closeSearch();
            } else {
                openSearch();
            }
        });
    }

    function openSearch() {
        searchForm.classList.remove('w-0', 'opacity-0');
        searchForm.classList.add('w-auto', 'opacity-100', 'ml-2');
        isSearchOpen = true;
        setTimeout(() => searchInput?.focus(), 100);
    }

    function closeSearch() {
        searchForm.classList.add('w-0', 'opacity-0');
        searchForm.classList.remove('w-auto', 'opacity-100', 'ml-2');
        isSearchOpen = false;
        hideAutocomplete();
        if (searchInput) searchInput.value = '';
    }

    // Close on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isSearchOpen) {
            closeSearch();
        }
    });

    // Close when clicking outside
    document.addEventListener('click', function (e) {
        if (isSearchOpen && !e.target.closest('#search-container')) {
            closeSearch();
        }
    });

    if (!searchInput || !autocompleteContainer) return;

    let debounceTimer;
    let currentFocus = -1;

    // Handle input changes with debounce
    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            hideAutocomplete();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });

    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function (e) {
        const items = autocompleteContainer.querySelectorAll('.autocomplete-item');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentFocus++;
            setActiveItem(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            setActiveItem(items);
        } else if (e.key === 'Enter' && currentFocus > -1) {
            e.preventDefault();
            if (items[currentFocus]) {
                items[currentFocus].click();
            }
        }
    });

    function fetchSuggestions(query) {
        fetch(`/search/autocomplete?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(products => {
                renderSuggestions(products, query);
            })
            .catch(error => {
                console.error('Search error:', error);
                hideAutocomplete();
            });
    }

    function renderSuggestions(products, query) {
        autocompleteContainer.innerHTML = '';
        currentFocus = -1;

        if (products.length === 0) {
            autocompleteContainer.innerHTML = `
                <div class="px-4 py-3 text-gray-400 font-cinzel text-sm text-center">
                    No results for "${query}"
                </div>
            `;
            autocompleteContainer.classList.remove('hidden');
            return;
        }

        products.forEach((product, index) => {
            const item = document.createElement('a');
            item.href = `/search?q=${encodeURIComponent(product.name)}`;
            item.className =
                'autocomplete-item flex items-center gap-3 px-4 py-2 hover:bg-red-900/30 transition-colors cursor-pointer border-b border-red-900/20 last:border-b-0';
            item.dataset.index = index;

            const imageUrl = product.image_url
                ? product.image_url.startsWith('http')
                    ? product.image_url
                    : `/${product.image_url}`
                : '/images/mask-logo.png';

            item.innerHTML = `
                <img src="${imageUrl}" alt="${product.name}" class="w-10 h-10 object-cover rounded border border-red-900/30">
                <div class="flex-1 min-w-0">
                    <div class="font-cinzel text-xs text-[#D8C9AE] truncate">
                        ${highlightMatch(product.name, query)}
                    </div>
                    <div class="text-red-700 font-cinzel text-sm">
                        $${Number.parseFloat(product.price).toFixed(2)}
                    </div>
                </div>
            `;

            item.addEventListener('mouseenter', function () {
                currentFocus = index;
                setActiveItem(autocompleteContainer.querySelectorAll('.autocomplete-item'));
            });

            autocompleteContainer.appendChild(item);
        });

        // Add "View all" link
        const viewAll = document.createElement('a');
        viewAll.href = `/search?q=${encodeURIComponent(query)}`;
        viewAll.className =
            'block px-4 py-2 text-center text-red-500 hover:text-red-400 hover:bg-red-900/20 font-cinzel text-xs transition-colors border-t border-red-900/30';
        viewAll.textContent = 'View all results →';
        autocompleteContainer.appendChild(viewAll);

        autocompleteContainer.classList.remove('hidden');
    }

    function highlightMatch(text, query) {
        const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
        return text.replaceAll(regex, '<span class="text-red-500">$1</span>');
    }

    function escapeRegex(string) {
        return string.replaceAll(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function setActiveItem(items) {
        items.forEach(item => item.classList.remove('bg-red-900/30'));

        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;

        if (items[currentFocus]) {
            items[currentFocus].classList.add('bg-red-900/30');
        }
    }

    function hideAutocomplete() {
        if (autocompleteContainer) {
            autocompleteContainer.classList.add('hidden');
        }
        currentFocus = -1;
    }
});
