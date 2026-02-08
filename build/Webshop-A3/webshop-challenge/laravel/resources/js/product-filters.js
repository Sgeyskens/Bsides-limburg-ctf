/**
 * Product Filters functionality
 * Handles filtering, sorting, and dynamic product grid updates
 */

document.addEventListener('DOMContentLoaded', function () {
    const filterSidebar = document.getElementById('filter-sidebar');
    if (!filterSidebar) return;

    const productType = filterSidebar.dataset.productType;
    const productsGrid = document.getElementById('products-grid');
    const productsCount = document.getElementById('products-count');
    const loadingOverlay = document.getElementById('loading-overlay');
    const noResults = document.getElementById('no-results');
    const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
    const mobileFilterClose = document.getElementById('mobile-filter-close');
    const filterContainer = document.getElementById('filter-container');

    let debounceTimer = null;
    let currentSortOrder = document.getElementById('sort-asc')?.classList.contains('bg-red-900/50') ? 'asc' : 'desc';

    // Mobile filter toggle
    if (mobileFilterToggle && filterContainer) {
        mobileFilterToggle.addEventListener('click', () => {
            filterContainer.classList.remove('hidden');
            filterContainer.classList.add('fixed', 'inset-0', 'z-50', 'overflow-y-auto', 'p-4', 'bg-black/95');
        });
    }

    if (mobileFilterClose && filterContainer) {
        mobileFilterClose.addEventListener('click', () => {
            filterContainer.classList.add('hidden');
            filterContainer.classList.remove('fixed', 'inset-0', 'z-50', 'overflow-y-auto', 'p-4', 'bg-black/95');
        });
    }

    // Sort order buttons
    const sortAscBtn = document.getElementById('sort-asc');
    const sortDescBtn = document.getElementById('sort-desc');

    if (sortAscBtn) {
        sortAscBtn.addEventListener('click', () => {
            currentSortOrder = 'asc';
            updateSortButtons();
            applyFilters();
        });
    }

    if (sortDescBtn) {
        sortDescBtn.addEventListener('click', () => {
            currentSortOrder = 'desc';
            updateSortButtons();
            applyFilters();
        });
    }

    function updateSortButtons() {
        if (sortAscBtn && sortDescBtn) {
            if (currentSortOrder === 'asc') {
                sortAscBtn.classList.add('bg-red-900/50', 'border-red-700', 'text-white');
                sortAscBtn.classList.remove('bg-black/50', 'border-red-900/50', 'text-gray-400');
                sortDescBtn.classList.remove('bg-red-900/50', 'border-red-700', 'text-white');
                sortDescBtn.classList.add('bg-black/50', 'border-red-900/50', 'text-gray-400');
            } else {
                sortDescBtn.classList.add('bg-red-900/50', 'border-red-700', 'text-white');
                sortDescBtn.classList.remove('bg-black/50', 'border-red-900/50', 'text-gray-400');
                sortAscBtn.classList.remove('bg-red-900/50', 'border-red-700', 'text-white');
                sortAscBtn.classList.add('bg-black/50', 'border-red-900/50', 'text-gray-400');
            }
        }
    }

    // Sort by select
    const sortBySelect = document.getElementById('sort-by');
    if (sortBySelect) {
        sortBySelect.addEventListener('change', () => {
            applyFilters();
        });
    }

    // Price inputs with debounce
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');

    if (minPriceInput) {
        minPriceInput.addEventListener('input', () => {
            debounceApplyFilters();
        });
    }

    if (maxPriceInput) {
        maxPriceInput.addEventListener('input', () => {
            debounceApplyFilters();
        });
    }

    // Rating radio buttons
    const ratingInputs = document.querySelectorAll('input[name="min_rating"]');
    ratingInputs.forEach(input => {
        input.addEventListener('change', () => {
            applyFilters();
        });
    });

    // Property checkboxes
    const propertyCheckboxes = document.querySelectorAll('input[name^="properties["]');
    propertyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            applyFilters();
        });
    });

    // Clear filters button
    const clearFiltersBtn = document.getElementById('clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
            clearAllFilters();
        });
    }

    // Mobile apply button
    const applyFiltersMobile = document.getElementById('apply-filters-mobile');
    if (applyFiltersMobile && filterContainer) {
        applyFiltersMobile.addEventListener('click', () => {
            filterContainer.classList.add('hidden');
            filterContainer.classList.remove('fixed', 'inset-0', 'z-50', 'overflow-y-auto', 'p-4', 'bg-black/95');
            applyFilters();
        });
    }

    function debounceApplyFilters() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            applyFilters();
        }, 300);
    }

    function clearAllFilters() {
        // Reset price inputs
        if (minPriceInput) minPriceInput.value = '';
        if (maxPriceInput) maxPriceInput.value = '';

        // Reset sort
        if (sortBySelect) sortBySelect.value = 'name';
        currentSortOrder = 'asc';
        updateSortButtons();

        // Reset rating
        const allRatingsRadio = document.querySelector('input[name="min_rating"][value=""]');
        if (allRatingsRadio) allRatingsRadio.checked = true;

        // Reset property checkboxes
        propertyCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        applyFilters(true);
    }

    function collectFilters() {
        const filters = {
            sort_by: sortBySelect?.value || 'name',
            sort_order: currentSortOrder,
        };

        // Price filters
        if (minPriceInput?.value) {
            filters.min_price = minPriceInput.value;
        }
        if (maxPriceInput?.value) {
            filters.max_price = maxPriceInput.value;
        }

        // Rating filter
        const selectedRating = document.querySelector('input[name="min_rating"]:checked');
        if (selectedRating?.value) {
            filters.min_rating = selectedRating.value;
        }

        // Property filters
        const properties = {};
        propertyCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                // Extract property name from input name (e.g., "properties[Genre][]")
                const match = checkbox.name.match(/properties\[([^\]]+)\]/);
                if (match) {
                    const propName = match[1];
                    if (!properties[propName]) {
                        properties[propName] = [];
                    }
                    properties[propName].push(checkbox.value);
                }
            }
        });

        if (Object.keys(properties).length > 0) {
            filters.properties = properties;
        }

        return filters;
    }

    function buildQueryString(filters) {
        const params = new URLSearchParams();

        if (filters.sort_by) params.append('sort_by', filters.sort_by);
        if (filters.sort_order) params.append('sort_order', filters.sort_order);
        if (filters.min_price) params.append('min_price', filters.min_price);
        if (filters.max_price) params.append('max_price', filters.max_price);
        if (filters.min_rating) params.append('min_rating', filters.min_rating);

        if (filters.properties) {
            for (const [propName, values] of Object.entries(filters.properties)) {
                values.forEach(value => {
                    params.append(`properties[${propName}][]`, value);
                });
            }
        }

        return params.toString();
    }

    async function applyFilters(isClearing = false) {
        const filters = collectFilters();

        // Show loading state
        if (loadingOverlay) {
            loadingOverlay.classList.remove('hidden');
        }

        // Update URL without reloading
        const queryString = isClearing ? 'clear_filters=1' : buildQueryString(filters);
        const newUrl = `${window.location.pathname}${queryString ? '?' + queryString : ''}`;
        window.history.replaceState({}, '', newUrl);

        try {
            const response = await fetch(`/products/${productType}/filter?${queryString}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Filter request failed');
            }

            const data = await response.json();

            // Update product count
            if (productsCount) {
                productsCount.textContent = `${data.count} product${data.count !== 1 ? 's' : ''} found`;
            }

            // Update product grid
            if (productsGrid) {
                if (data.products.length === 0) {
                    productsGrid.innerHTML = '';
                    if (noResults) {
                        noResults.classList.remove('hidden');
                    }
                } else {
                    if (noResults) {
                        noResults.classList.add('hidden');
                    }
                    productsGrid.innerHTML = data.products.map(product => renderProductCard(product)).join('');
                }
            }
        } catch (error) {
            console.error('Error applying filters:', error);
        } finally {
            // Hide loading state
            if (loadingOverlay) {
                loadingOverlay.classList.add('hidden');
            }
        }
    }

    function renderProductCard(product) {
        const imageUrl = product.image_url
            ? (product.image_url.startsWith('http') ? product.image_url : `/${product.image_url}`)
            : '/images/mask-logo.png';

        const isLoggedIn = document.body.dataset.authenticated === 'true';
        const isTShirt = product.name.includes('T-Shirt');
        const productType = product.product_type;

        // Generate star rating HTML
        const avgRating = product.avg_rating || 0;
        const ratingCount = product.rating_count || 0;
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            const fillColor = i <= Math.round(avgRating) ? 'text-red-700' : 'text-gray-600';
            starsHtml += `<svg class="w-3 h-3 ${fillColor}" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>`;
        }

        const ratingHtml = ratingCount > 0 ? `
            <div class="flex items-center gap-1 mb-2">
                <div class="flex">${starsHtml}</div>
                <span class="text-xs text-gray-400">(${ratingCount})</span>
            </div>
        ` : '';

        let buttonHtml;
        if (isLoggedIn) {
            const sizeSelector = isTShirt ? `
                <select class="size-select w-full bg-black/50 border-2 border-red-900/50 rounded px-2 py-1.5 font-cinzel text-xs text-white focus:border-red-700 focus:outline-none transition-colors mb-2">
                    <option value="">Select Size</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                </select>
            ` : '';

            buttonHtml = `
                ${sizeSelector}
                <button data-add-to-cart data-product-id="${product.product_id}" data-product-type="${productType}" class="product-card flex items-center justify-center gap-2 px-3 py-1.5 bg-red-900/50 hover:bg-red-900 text-white font-cinzel text-xs uppercase rounded border border-red-700 transition-all hover:scale-105">
                    <img src="/images/mask-logo.png" alt="Jason Mask" class="w-4 h-4">
                    Add to Cart
                </button>
            `;
        } else {
            buttonHtml = `
                <a href="/login" class="flex items-center justify-center gap-2 px-3 py-1.5 bg-gray-800/50 hover:bg-gray-700 text-gray-300 font-cinzel text-xs uppercase rounded border border-gray-600 transition-all hover:scale-105">
                    <img src="/images/mask-logo.png" alt="Jason Mask" class="w-4 h-4 opacity-50">
                    Login to Buy
                </a>
            `;
        }

        return `
            <div class="flex flex-col h-full bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg overflow-hidden transition-all hover:border-red-700 hover:scale-105 hover:shadow-xl hover:shadow-red-900/30">
                <div class="aspect-[2/3] bg-gradient-to-br from-red-950/30 to-black flex items-center justify-center border-b-2 border-red-900/50">
                    <img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover" onerror="this.src='/images/mask-logo.png'; this.classList.add('w-20', 'h-20', 'opacity-60'); this.classList.remove('w-full', 'h-full');">
                </div>
                <div class="flex flex-col flex-grow p-3">
                    <h3 class="font-cinzel text-xs md:text-sm text-[#D8C9AE] mb-1 uppercase tracking-wide line-clamp-2">
                        ${product.name}
                    </h3>
                    ${product.description ? `<p class="text-xs text-gray-400 mb-2 line-clamp-2">${product.description}</p>` : ''}
                    ${ratingHtml}
                    <div class="flex flex-col gap-2 mt-auto product-card">
                        <span class="text-red-700 font-bold text-lg font-cinzel">
                            $${Number.parseFloat(product.price).toFixed(2)}
                        </span>
                        ${buttonHtml}
                    </div>
                </div>
            </div>
        `;
    }

});
