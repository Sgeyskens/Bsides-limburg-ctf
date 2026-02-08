<!-- Footer -->
<footer class="relative z-50 pt-12 pb-8 {{ $transparentFooter ?? false ? 'bg-black/50 backdrop-blur-sm' : 'bg-black' }}">
    <!-- Logo Section -->
    <div class="max-w-7xl mx-auto px-8 lg:px-16 flex flex-col items-center text-center">
        <!-- Friday 13th Logo -->
        <img src="{{ asset('images/footer-logo.png') }}" alt="Friday the 13th" class="w-48 md:w-48 mb-6">
    </div>

    <!-- Divider Line - Slightly off edges -->
    <div class="px-4 mb-4">
        <div class="w-full h-px bg-red-700"></div>
    </div>

    <!-- Bottom Section -->
    <div class="max-w-7xl mx-auto px-8 lg:px-16 flex flex-col items-center text-center">
        <!-- Copyright Text -->
        <p class="font-cinzel text-grey text-sm md:text-base mb-2">
            ©1980 - 2026 Camp Crystal Lake Ltd. | No rights Reserved
        </p>

        <!-- Tagline -->
        <p class="friday13-title text-white text-base md:text-sm">
            WE ARE DYING TO HAVE YOU...
        </p>
    </div>
</footer>
