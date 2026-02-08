<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Checkout - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden">

    <!-- Background Section -->
    <div class="fixed inset-0 z-0">
        <img src="{{ asset('images/hero-sky.png') }}" alt="Red Sky Background" class="absolute inset-0 w-full h-full object-cover object-center">
        <div class="md:hidden absolute inset-0 z-5">
            <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="w-full h-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-b from-red-900/50 via-red-900/30 to-black/70"></div>
        </div>
        <img src="{{ asset('images/mountains.png') }}" alt="Mountains" class="hidden md:block absolute bottom-0 left-0 w-full h-auto z-10">
        <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="absolute bottom-0 right-0 h-[350px] lg:h-[450px] w-auto object-contain z-20 hidden md:block">
        <img src="{{ asset('images/watchtower.png') }}" alt="Watchtower" class="absolute bottom-0 right-[180px] lg:right-[300px] h-40 lg:h-70 w-auto object-contain z-30 hidden md:block">
    </div>

    <!-- Content Wrapper -->
    <div class="relative z-40 min-h-screen flex flex-col">
        <!-- Navigation -->
        <div class="px-4 md:px-8 lg:px-16 py-4 max-w-7xl w-full mx-auto">
            @include('partials.navbar')
        </div>

        <!-- Main Content -->
        <main class="flex-1 px-4 md:px-8 lg:px-16 py-8 max-w-7xl w-full mx-auto">

            <!-- Page Title -->
            <div class="text-center mb-12">
                <h1 class="friday13-title text-5xl md:text-6xl lg:text-7xl uppercase tracking-wider mb-4">
                    CHECKOUT
                </h1>
            </div>

            <!-- Flash Messages -->
            @if(session('error'))
                <div class="bg-red-900/50 border border-red-700 text-white px-4 py-3 rounded mb-6 font-cinzel">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Shipping Address -->
                        <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6">
                            <h2 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider mb-6 border-b border-red-900/50 pb-4">
                                Shipping Address
                            </h2>

                            <div class="space-y-4">
                                <div>
                                    <label for="shipping_street" class="block font-cinzel text-sm text-gray-300 mb-2">Street Address</label>
                                    <input
                                        type="text"
                                        name="shipping_street"
                                        id="shipping_street"
                                        required
                                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                        placeholder="123 Main Street"
                                        value="{{ old('shipping_street') }}"
                                    >
                                    @error('shipping_street')
                                        <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_city" class="block font-cinzel text-sm text-gray-300 mb-2">City</label>
                                        <input
                                            type="text"
                                            name="shipping_city"
                                            id="shipping_city"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="Crystal Lake"
                                            value="{{ old('shipping_city') }}"
                                        >
                                        @error('shipping_city')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_state" class="block font-cinzel text-sm text-gray-300 mb-2">State / Province</label>
                                        <input
                                            type="text"
                                            name="shipping_state"
                                            id="shipping_state"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="New Jersey"
                                            value="{{ old('shipping_state') }}"
                                        >
                                        @error('shipping_state')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_zip" class="block font-cinzel text-sm text-gray-300 mb-2">ZIP / Postal Code</label>
                                        <input
                                            type="text"
                                            name="shipping_zip"
                                            id="shipping_zip"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="07456"
                                            value="{{ old('shipping_zip') }}"
                                        >
                                        @error('shipping_zip')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="shipping_country" class="block font-cinzel text-sm text-gray-300 mb-2">Country</label>
                                        <input
                                            type="text"
                                            name="shipping_country"
                                            id="shipping_country"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="United States"
                                            value="{{ old('shipping_country') }}"
                                        >
                                        @error('shipping_country')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6">
                            <h2 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider mb-6 border-b border-red-900/50 pb-4">
                                Billing Address
                            </h2>

                            <div class="mb-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="same-as-shipping" class="w-4 h-4 accent-red-700">
                                    <span class="font-cinzel text-sm text-gray-300">Same as shipping address</span>
                                </label>
                            </div>

                            <div id="billing-fields" class="space-y-4">
                                <div>
                                    <label for="billing_street" class="block font-cinzel text-sm text-gray-300 mb-2">Street Address</label>
                                    <input
                                        type="text"
                                        name="billing_street"
                                        id="billing_street"
                                        required
                                        class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                        placeholder="123 Main Street"
                                        value="{{ old('billing_street') }}"
                                    >
                                    @error('billing_street')
                                        <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="billing_city" class="block font-cinzel text-sm text-gray-300 mb-2">City</label>
                                        <input
                                            type="text"
                                            name="billing_city"
                                            id="billing_city"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="Crystal Lake"
                                            value="{{ old('billing_city') }}"
                                        >
                                        @error('billing_city')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="billing_state" class="block font-cinzel text-sm text-gray-300 mb-2">State / Province</label>
                                        <input
                                            type="text"
                                            name="billing_state"
                                            id="billing_state"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="New Jersey"
                                            value="{{ old('billing_state') }}"
                                        >
                                        @error('billing_state')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="billing_zip" class="block font-cinzel text-sm text-gray-300 mb-2">ZIP / Postal Code</label>
                                        <input
                                            type="text"
                                            name="billing_zip"
                                            id="billing_zip"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="07456"
                                            value="{{ old('billing_zip') }}"
                                        >
                                        @error('billing_zip')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="billing_country" class="block font-cinzel text-sm text-gray-300 mb-2">Country</label>
                                        <input
                                            type="text"
                                            name="billing_country"
                                            id="billing_country"
                                            required
                                            class="w-full bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors"
                                            placeholder="United States"
                                            value="{{ old('billing_country') }}"
                                        >
                                        @error('billing_country')
                                            <span class="text-red-500 text-sm font-cinzel">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discount Code -->
                        <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6">
                            <h2 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider mb-6 border-b border-red-900/50 pb-4">
                                Discount Code
                            </h2>

                            <div class="flex gap-4">
                                <input
                                    type="text"
                                    id="discount-code-input"
                                    form="discount-form-dummy"
                                    class="flex-1 bg-black/50 border-2 border-red-900/50 rounded px-4 py-3 font-cinzel text-white placeholder-gray-500 focus:border-red-700 focus:outline-none transition-colors uppercase"
                                    placeholder="Enter code"
                                >
                                <button
                                    type="button"
                                    id="apply-discount-btn"
                                    class="font-cinzel px-6 py-3 bg-red-900/50 hover:bg-red-900 text-white uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:scale-105 rounded border border-red-700"
                                >
                                    Apply
                                </button>
                            </div>
                            <input type="hidden" name="discount_code" id="discount-code-hidden">
                            <p id="discount-message" class="mt-2 font-cinzel text-sm hidden"></p>

                            <!-- CTF Flag Display (shown when race condition exploited) -->
                            <div id="ctf-flag-container" class="{{ isset($raceConditionFlag) && $raceConditionFlag ? '' : 'hidden' }} mt-4 p-4 bg-green-900/50 border-2 border-green-500 rounded-lg animate-pulse">
                                <p class="font-cinzel text-green-400 text-sm uppercase tracking-wider mb-2">Race Condition Exploited!</p>
                                <code id="ctf-flag" class="block bg-black/50 p-3 rounded text-green-300 font-mono text-sm break-all">{{ $raceConditionFlag ?? '' }}</code>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 sticky top-4">
                            <h2 class="font-cinzel text-xl text-[#D8C9AE] uppercase tracking-wider mb-6 border-b border-red-900/50 pb-4">
                                Order Summary
                            </h2>

                            <!-- Order Items -->
                            <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                                @foreach($cart->items as $item)
                                    @if($item->product)
                                        <div class="flex justify-between items-start font-cinzel text-sm">
                                            <div class="flex-1">
                                                <span class="text-gray-300">{{ $item->product->name }}</span>
                                                <span class="text-gray-500 text-xs block">
                                                    x{{ $item->quantity }}
                                                    @if($item->size) ({{ $item->size }}) @endif
                                                </span>
                                            </div>
                                            <span class="text-[#D8C9AE]">${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="border-t border-red-900/50 pt-4 space-y-3 mb-6">
                                <div class="flex justify-between font-cinzel text-gray-300">
                                    <span>Subtotal</span>
                                    <span id="checkout-subtotal">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between font-cinzel text-gray-300">
                                    <span>Shipping</span>
                                    <span class="text-green-500">FREE</span>
                                </div>
                                <div id="discount-row" class="font-cinzel text-green-500 {{ $cart->discount_amount > 0 ? '' : 'hidden' }}">
                                    <div class="flex justify-between items-center">
                                        <span>Discount (<span id="discount-code-display">{{ $cart->discount_code }}</span>)</span>
                                        <span id="discount-amount">-${{ number_format($cart->discount_amount, 2) }}</span>
                                    </div>
                                    <button type="button" id="remove-discount-btn" class="text-red-400 hover:text-red-300 text-xs underline mt-1" title="Remove discount">remove</button>
                                </div>
                            </div>

                            <div class="border-t border-red-900/50 pt-4 mb-6">
                                <div class="flex justify-between font-cinzel text-lg text-[#D8C9AE] font-bold">
                                    <span>Total</span>
                                    <span id="checkout-total">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="block w-full text-center font-cinzel px-6 py-3 bg-red-900 hover:bg-red-800 text-white uppercase tracking-widest text-sm font-bold cursor-pointer transition-all hover:scale-105 rounded border border-red-700"
                            >
                                Place Order
                            </button>

                            <a href="{{ route('cart.index') }}" class="block w-full text-center font-cinzel px-6 py-3 mt-4 bg-transparent hover:bg-red-900/30 text-[#D8C9AE] uppercase tracking-widest text-xs font-bold cursor-pointer transition-all rounded border border-red-900/50">
                                Back to Cart
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <div class="relative z-50">
        @include('partials.footer')
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 bg-black/90 border-2 border-red-700 text-white px-6 py-4 rounded-lg font-cinzel transform translate-y-20 opacity-0 transition-all duration-300 z-50">
        <span id="toast-message"></span>
    </div>

    <script>
        // Address field pairs
        const addressFields = ['street', 'city', 'state', 'zip', 'country'];

        // Same as shipping checkbox
        document.getElementById('same-as-shipping').addEventListener('change', function() {
            const billingFieldsContainer = document.getElementById('billing-fields');

            if (this.checked) {
                // Copy values from shipping to billing
                addressFields.forEach(field => {
                    const shippingField = document.getElementById('shipping_' + field);
                    const billingField = document.getElementById('billing_' + field);
                    billingField.value = shippingField.value;
                    billingField.setAttribute('readonly', true);
                    billingField.classList.add('opacity-50');
                });
            } else {
                // Remove readonly and opacity
                addressFields.forEach(field => {
                    const billingField = document.getElementById('billing_' + field);
                    billingField.removeAttribute('readonly');
                    billingField.classList.remove('opacity-50');
                });
            }
        });

        // Update billing when shipping changes (if same-as-shipping is checked)
        addressFields.forEach(field => {
            document.getElementById('shipping_' + field).addEventListener('input', function() {
                const checkbox = document.getElementById('same-as-shipping');
                if (checkbox.checked) {
                    document.getElementById('billing_' + field).value = this.value;
                }
            });
        });

        // Discount Code Application
        const applyDiscountBtn = document.getElementById('apply-discount-btn');
        const discountCodeInput = document.getElementById('discount-code-input');
        const discountMessage = document.getElementById('discount-message');
        const discountRow = document.getElementById('discount-row');
        const discountAmount = document.getElementById('discount-amount');
        const checkoutTotal = document.getElementById('checkout-total');
        const discountCodeHidden = document.getElementById('discount-code-hidden');
        const ctfFlagContainer = document.getElementById('ctf-flag-container');
        const ctfFlag = document.getElementById('ctf-flag');

        // Anti-spam protection - prevents rapid clicking
        let isRequestInProgress = false;
        let lastRequestTime = 0;
        const COOLDOWN_MS = 2000; // 2 second cooldown between requests
        let discountAlreadyApplied = {{ $cart->discount_amount > 0 ? 'true' : 'false' }};

        // Generate unique nonce for each request (server uses this to deduplicate)
        function generateNonce() {
            return Date.now().toString(36) + Math.random().toString(36).substr(2);
        }

        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            toastMessage.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.toggle('border-red-700', isError);
            toast.classList.toggle('border-green-700', !isError);
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        applyDiscountBtn.addEventListener('click', async function() {
            // Immediate block - prevents double-click in browser (Burp Suite bypasses JS)
            if (applyDiscountBtn.disabled) return;
            applyDiscountBtn.disabled = true;

            // Check if discount already applied - only one discount allowed
            if (discountAlreadyApplied) {
                showToast('A discount code has already been applied', true);
                applyDiscountBtn.disabled = false;
                return;
            }

            // Anti-spam: Check if request is already in progress
            if (isRequestInProgress) {
                showToast('Please wait for the current request to complete', true);
                applyDiscountBtn.disabled = false;
                return;
            }

            // Anti-spam: Check cooldown
            const now = Date.now();
            if (now - lastRequestTime < COOLDOWN_MS) {
                const remaining = Math.ceil((COOLDOWN_MS - (now - lastRequestTime)) / 1000);
                showToast(`Please wait ${remaining} second(s) before trying again`, true);
                applyDiscountBtn.disabled = false;
                return;
            }

            const code = discountCodeInput.value.trim();
            if (!code) {
                showToast('Please enter a discount code', true);
                applyDiscountBtn.disabled = false;
                return;
            }

            isRequestInProgress = true;
            lastRequestTime = now;
            discountAlreadyApplied = true; // Lock immediately - only Burp can bypass this
            applyDiscountBtn.disabled = true;
            applyDiscountBtn.textContent = 'Applying...';
            discountCodeInput.disabled = true;

            try {
                const nonce = generateNonce();
                const response = await fetch('{{ route("checkout.apply-discount") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: code, _token_nonce: nonce })
                });

                const data = await response.json();

                if (data.success) {
                    // Update UI with discount
                    discountRow.classList.remove('hidden');
                    discountAmount.textContent = '-$' + data.discount;
                    checkoutTotal.textContent = '$' + data.total;
                    discountCodeHidden.value = data.code;

                    // Show success message
                    discountMessage.textContent = 'Discount applied: ' + (data.percentage ? data.percentage + '% off' : '$' + data.discount + ' off');
                    discountMessage.classList.remove('hidden', 'text-red-500');
                    discountMessage.classList.add('text-green-500');

                    showToast('Discount code applied!');

                    // Keep button visually disabled
                    applyDiscountBtn.classList.add('opacity-50', 'cursor-not-allowed');

                    // Check for CTF flag (race condition exploited)
                    if (data.flag) {
                        ctfFlagContainer.classList.remove('hidden');
                        ctfFlag.textContent = data.flag;
                        showToast('FLAG CAPTURED! Race condition exploited!');
                    }

                    // Update discount code display
                    document.getElementById('discount-code-display').textContent = data.code;
                } else {
                    // Request failed - unlock so user can try again
                    discountAlreadyApplied = false;
                    discountCodeInput.disabled = false;
                    discountMessage.textContent = data.message;
                    discountMessage.classList.remove('hidden', 'text-green-500');
                    discountMessage.classList.add('text-red-500');
                    showToast(data.message, true);
                }
            } catch (error) {
                // Request error - unlock so user can try again
                discountAlreadyApplied = false;
                discountCodeInput.disabled = false;
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', true);
            } finally {
                isRequestInProgress = false;
                applyDiscountBtn.textContent = 'Apply';
                // Only re-enable button if discount was not applied
                if (!discountAlreadyApplied) {
                    applyDiscountBtn.disabled = false;
                }
            }
        });

        // Allow pressing Enter to apply discount
        discountCodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                applyDiscountBtn.click();
            }
        });

        // Disable input and button if discount already applied on page load
        if (discountAlreadyApplied) {
            applyDiscountBtn.classList.add('opacity-50', 'cursor-not-allowed');
            discountCodeInput.disabled = true;
            discountCodeInput.placeholder = 'Discount already applied';
        }

        // Remove Discount functionality
        const removeDiscountBtn = document.getElementById('remove-discount-btn');
        const discountCodeDisplay = document.getElementById('discount-code-display');

        removeDiscountBtn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to remove this discount code?')) {
                return;
            }

            removeDiscountBtn.disabled = true;
            removeDiscountBtn.textContent = 'removing...';

            try {
                const response = await fetch('{{ route("checkout.remove-discount") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Hide discount row
                    discountRow.classList.add('hidden');

                    // Update total
                    checkoutTotal.textContent = '$' + data.total;

                    // Reset discount code hidden input
                    discountCodeHidden.value = '';

                    // Hide any flag displays
                    ctfFlagContainer.classList.add('hidden');

                    // Reset discount message
                    discountMessage.classList.add('hidden');
                    discountMessage.textContent = '';

                    // Re-enable discount input
                    discountAlreadyApplied = false;
                    applyDiscountBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    applyDiscountBtn.disabled = false;
                    discountCodeInput.disabled = false;
                    discountCodeInput.value = '';
                    discountCodeInput.placeholder = 'Enter code';

                    showToast('Discount code removed');
                } else {
                    showToast(data.message || 'Error removing discount', true);
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', true);
            } finally {
                removeDiscountBtn.disabled = false;
                removeDiscountBtn.textContent = 'remove';
            }
        });

        // Poll for race condition exploit detection
        let pollingInterval = null;
        let flagCaptured = {{ isset($raceConditionFlag) && $raceConditionFlag ? 'true' : 'false' }};

        function startPolling() {
            if (flagCaptured || pollingInterval) return;

            pollingInterval = setInterval(async () => {
                try {
                    const response = await fetch('{{ route("checkout.status") }}', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();

                    // Update discount display
                    if (data.discount_code) {
                        discountRow.classList.remove('hidden');
                        discountAmount.textContent = '-$' + data.discount_amount;
                        checkoutTotal.textContent = '$' + data.total;
                        document.getElementById('discount-code-display').textContent = data.discount_code;
                    }

                    // Check for flag
                    if (data.flag && !flagCaptured) {
                        flagCaptured = true;
                        ctfFlagContainer.classList.remove('hidden');
                        ctfFlag.textContent = data.flag;
                        showToast('FLAG CAPTURED! Race condition exploited!');
                        clearInterval(pollingInterval);
                    }
                } catch (e) {
                    console.error('Polling error:', e);
                }
            }, 1000); // Check every second
        }

        // Start polling when page loads
        startPolling();
    </script>
</body>
</html>
