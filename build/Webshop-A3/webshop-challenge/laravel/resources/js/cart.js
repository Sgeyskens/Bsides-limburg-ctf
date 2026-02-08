/**
 * Cart functionality for Friday the 13th Webshop
 */

// Toast notification helper
function showToast(message, isError = false) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    if (!toast || !toastMessage) return;

    toastMessage.textContent = message;
    toast.classList.remove('translate-y-20', 'opacity-0');
    toast.classList.toggle('border-red-700', !isError);
    toast.classList.toggle('border-green-700', !isError);

    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
    }, 3000);
}

// Get CSRF token
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

// Update cart badge in navbar
function updateCartBadge(count) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    });
}

// Add to cart handler
function initAddToCart() {
    document.addEventListener('click', function(e) {
        const addButton = e.target.closest('[data-add-to-cart]');
        if (!addButton) return;

        e.preventDefault();

        const productId = addButton.dataset.productId;
        const productType = addButton.dataset.productType;

        // For merch items, get the selected size
        let size = null;
        if (productType === 'merch') {
            const sizeSelect = addButton.closest('.product-card')?.querySelector('.size-select');
            if (sizeSelect) {
                size = sizeSelect.value;
                if (!size) {
                    showToast('Please select a size', true);
                    return;
                }
            }
        }

        // Disable button during request
        addButton.disabled = true;
        const originalText = addButton.innerHTML;
        addButton.innerHTML = '<span class="animate-pulse">Adding...</span>';

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1,
                size: size
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                updateCartBadge(data.cartCount);
            } else {
                showToast(data.message || 'Error adding item to cart', true);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error adding item to cart', true);
        })
        .finally(() => {
            addButton.disabled = false;
            addButton.innerHTML = originalText;
        });
    });
}

// Cart page: Update quantity
function initQuantityControls() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.quantity-btn');
        if (!btn) return;

        const itemId = btn.dataset.itemId;
        const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
        if (!cartItem) return;

        const quantitySpan = cartItem.querySelector('.item-quantity');
        let currentQuantity = Number.parseInt(quantitySpan.textContent);

        if (btn.classList.contains('increase')) {
            if (currentQuantity >= 10) {
                showToast('Maximum quantity is 10', true);
                return;
            }
            currentQuantity++;
        } else if (btn.classList.contains('decrease')) {
            if (currentQuantity <= 1) {
                // Remove item instead
                removeCartItem(itemId);
                return;
            }
            currentQuantity--;
        }

        updateCartItemQuantity(itemId, currentQuantity);
    });
}

// Update cart item quantity via AJAX
function updateCartItemQuantity(itemId, quantity) {
    fetch(`/cart/update/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
            if (cartItem) {
                cartItem.querySelector('.item-quantity').textContent = quantity;
                cartItem.querySelector('.item-total').textContent = '$' + data.itemTotal;
            }

            // Update totals
            const subtotal = document.getElementById('cart-subtotal');
            const total = document.getElementById('cart-total');
            if (subtotal) subtotal.textContent = '$' + data.subtotal;
            if (total) total.textContent = '$' + data.subtotal;

            updateCartBadge(data.cartCount);

            // Notify user if discount was removed
            if (data.discountRemoved) {
                showToast('Discount code removed - cart no longer meets minimum purchase requirement', true);
            }
        } else {
            showToast(data.message || 'Error updating cart', true);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating cart', true);
    });
}

// Remove cart item
function initRemoveItem() {
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-item');
        if (!removeBtn) return;

        const itemId = removeBtn.dataset.itemId;
        removeCartItem(itemId);
    });
}

function removeCartItem(itemId) {
    fetch(`/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartItem = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
            if (cartItem) {
                cartItem.remove();
            }

            // Update totals
            const subtotal = document.getElementById('cart-subtotal');
            const total = document.getElementById('cart-total');
            if (subtotal) subtotal.textContent = '$' + data.subtotal;
            if (total) total.textContent = '$' + data.subtotal;

            updateCartBadge(data.cartCount);
            showToast(data.message);

            // Notify user if discount was removed
            if (data.discountRemoved) {
                showToast('Discount code removed - cart no longer meets minimum purchase requirement', true);
            }

            // If cart is empty, reload to show empty state
            if (data.cartCount === 0) {
                globalThis.location.reload();
            }
        } else {
            showToast(data.message || 'Error removing item', true);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error removing item', true);
    });
}

// Checkout: Apply discount code
// NOTE: Discount code functionality is handled by inline script in checkout/index.blade.php
// to support race condition CTF challenge with nonce tokens
function initDiscountCode() {
    // Disabled - checkout page has its own implementation
    return;
}

// Fetch and update cart count on page load
function initCartCount() {
    fetch('/cart/count', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateCartBadge(data.count);
    })
    .catch(error => {
        console.error('Error fetching cart count:', error);
    });
}

// Initialize all cart functionality
document.addEventListener('DOMContentLoaded', function() {
    initAddToCart();
    initQuantityControls();
    initRemoveItem();
    initDiscountCode();

    // Only fetch cart count if user is logged in (cart link exists)
    if (document.querySelector('.cart-link')) {
        initCartCount();
    }
});

export { showToast, updateCartBadge };
