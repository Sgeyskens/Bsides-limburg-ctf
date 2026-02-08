<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Login - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden">
    <!-- Header Section -->
    <div class="relative bg-gradient-to-b from-red-900/20 to-black">
        <div class="relative z-40 px-4 md:px-8 lg:px-16 py-4 max-w-7xl w-full mx-auto">
            @include('partials.navbar')
        </div>
    </div>

    <!-- Main Content -->
    <main class="min-h-screen px-4 md:px-8 lg:px-16 py-16 max-w-7xl mx-auto flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="text-center mb-12">
                <h1 class="font-cinzel text-4xl md:text-5xl text-red-700 uppercase tracking-wider mb-4 font-bold">
                    LOGIN
                </h1>
                <p class="font-cinzel text-lg text-[#D8C9AE]">
                    Enter if you dare...
                </p>
            </div>

            <!-- Login Form Placeholder -->
            <div class="text-center py-20">
                <p class="font-cinzel text-xl text-[#D8C9AE]">Coming Soon...</p>
            </div>
        </div>
    </main>

    @include('partials.footer')
</body>
</html>
