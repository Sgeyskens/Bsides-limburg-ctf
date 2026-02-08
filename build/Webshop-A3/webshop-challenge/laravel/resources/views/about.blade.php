<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>About - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden">

    <!-- Background Section -->
    <div class="fixed inset-0 z-0">
        <!-- Desktop: Red Sky Background -->
        <img src="{{ asset('images/hero-sky.png') }}" alt="Red Sky Background" class="absolute inset-0 w-full h-full object-cover object-center">

        <!-- Mobile: Large Jason Background Overlay (on top of red sky) -->
        <div class="md:hidden absolute inset-0 z-5">
            <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="w-full h-full object-cover object-center">
            <!-- Red overlay for mobile to darken Jason image -->
            <div class="absolute inset-0 bg-gradient-to-b from-red-900/50 via-red-900/30 to-black/70"></div>
        </div>

        <!-- Mountains Silhouette Layer - Desktop Only -->
        <img src="{{ asset('images/mountains.png') }}" alt="Mountains" class="hidden md:block absolute bottom-0 left-0 w-full h-auto z-10">

        <!-- Jason Full Body - Desktop Only (right side) -->
        <img src="{{ asset('images/jason-full.png') }}" alt="Jason Voorhees" class="absolute bottom-0 right-0 h-[350px] lg:h-[450px] w-auto object-contain z-20 hidden md:block">

        <!-- Watchtower - Desktop Only -->
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
                    ABOUT
                </h1>
                <p class="font-cinzel text-base md:text-lg text-[#D8C9AE] max-w-3xl mx-auto">
                    The legend of Camp Crystal Lake
                </p>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">

                <!-- The Legend Section -->
                <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/mask-logo.png') }}" alt="Jason Mask" class="w-10 h-10">
                        <h2 class="font-cinzel text-xl md:text-2xl text-red-700 uppercase tracking-wide">The Legend</h2>
                    </div>
                    <div class="space-y-4 text-gray-300 font-cinzel text-sm md:text-base leading-relaxed">
                        <p>
                            In 1980, a new kind of terror was unleashed upon the world. <span class="text-[#D8C9AE]">Friday the 13th</span> emerged from the depths of Camp Crystal Lake to become one of the most iconic horror franchises in cinema history.
                        </p>
                        <p>
                            What began as a chilling tale of revenge has grown into a cultural phenomenon spanning over four decades, with twelve films, a television series, novels, comic books, video games, and countless merchandise items.
                        </p>
                        <p>
                            The hockey mask. The machete. The haunting "ki ki ki, ma ma ma" sound. These elements have become synonymous with horror itself, inspiring generations of fans and filmmakers alike.
                        </p>
                    </div>
                </div>

                <!-- Jason Voorhees Section -->
                <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <svg class="w-10 h-10 text-red-700" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9h10v2H7z"/>
                        </svg>
                        <h2 class="font-cinzel text-xl md:text-2xl text-red-700 uppercase tracking-wide">Jason Voorhees</h2>
                    </div>
                    <div class="space-y-4 text-gray-300 font-cinzel text-sm md:text-base leading-relaxed">
                        <p>
                            <span class="text-[#D8C9AE]">Jason Voorhees</span> has become the face of slasher horror. Born with severe facial deformities, young Jason was believed to have drowned at Camp Crystal Lake in 1957 due to negligent counselors.
                        </p>
                        <p>
                            His mother, Pamela Voorhees, sought revenge against the camp, but Jason himself emerged as the unstoppable force of vengeance in subsequent films, donning his iconic hockey mask in Part III.
                        </p>
                        <p>
                            Whether portrayed as a vengeful spirit, an undead revenant, or a supernatural entity, Jason remains one of horror's most recognizable and feared villains.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Timeline Section -->
            <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 md:p-8 mb-16">
                <div class="flex items-center gap-3 mb-8">
                    <svg class="w-10 h-10 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="font-cinzel text-xl md:text-2xl text-red-700 uppercase tracking-wide">The Legacy Timeline</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- 1980 -->
                    <div class="border-l-2 border-red-700 pl-4">
                        <span class="font-cinzel text-red-700 text-2xl font-bold">1980</span>
                        <h3 class="font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wide mt-1">The Beginning</h3>
                        <p class="text-gray-400 text-xs mt-2">The original Friday the 13th premieres, introducing Camp Crystal Lake and Pamela Voorhees.</p>
                    </div>

                    <!-- 1982 -->
                    <div class="border-l-2 border-red-700 pl-4">
                        <span class="font-cinzel text-red-700 text-2xl font-bold">1982</span>
                        <h3 class="font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wide mt-1">The Mask</h3>
                        <p class="text-gray-400 text-xs mt-2">Part III introduces Jason's iconic hockey mask, cementing his image in horror history.</p>
                    </div>

                    <!-- 2003 -->
                    <div class="border-l-2 border-red-700 pl-4">
                        <span class="font-cinzel text-red-700 text-2xl font-bold">2003</span>
                        <h3 class="font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wide mt-1">Crossover</h3>
                        <p class="text-gray-400 text-xs mt-2">Freddy vs. Jason unites two horror icons in an epic slasher showdown.</p>
                    </div>

                    <!-- 2017 -->
                    <div class="border-l-2 border-red-700 pl-4">
                        <span class="font-cinzel text-red-700 text-2xl font-bold">2017</span>
                        <h3 class="font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wide mt-1">The Game</h3>
                        <p class="text-gray-400 text-xs mt-2">Friday the 13th: The Game launches, letting players experience both sides of the horror.</p>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-16">
                <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 text-center">
                    <span class="font-cinzel text-4xl md:text-5xl text-red-700 font-bold">12</span>
                    <p class="font-cinzel text-xs md:text-sm text-[#D8C9AE] uppercase tracking-wide mt-2">Films</p>
                </div>
                <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 text-center">
                    <span class="font-cinzel text-4xl md:text-5xl text-red-700 font-bold">40+</span>
                    <p class="font-cinzel text-xs md:text-sm text-[#D8C9AE] uppercase tracking-wide mt-2">Years of Terror</p>
                </div>
                <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 text-center">
                    <span class="font-cinzel text-4xl md:text-5xl text-red-700 font-bold">$465M</span>
                    <p class="font-cinzel text-xs md:text-sm text-[#D8C9AE] uppercase tracking-wide mt-2">Box Office</p>
                </div>
                <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 text-center">
                    <span class="font-cinzel text-4xl md:text-5xl text-red-700 font-bold">#1</span>
                    <p class="font-cinzel text-xs md:text-sm text-[#D8C9AE] uppercase tracking-wide mt-2">Horror Icon</p>
                </div>
            </div>

            <!-- About the Store Section -->
            <div class="bg-black/80 backdrop-blur-sm border-2 border-red-900/50 rounded-lg p-6 md:p-8 mb-16">
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-10 h-10 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <h2 class="font-cinzel text-xl md:text-2xl text-red-700 uppercase tracking-wide">About Our Store</h2>
                </div>
                <div class="space-y-4 text-gray-300 font-cinzel text-sm md:text-base leading-relaxed">
                    <p>
                        Welcome to the official <span class="text-[#D8C9AE]">Friday the 13th</span> online store. We're dedicated to bringing fans the very best in Friday the 13th merchandise, from the complete film collection to exclusive collectibles you won't find anywhere else.
                    </p>
                    <p>
                        Whether you're a longtime fan who remembers seeing the original in theaters, or a newcomer who just discovered the terror of Camp Crystal Lake, we have something for every horror enthusiast.
                    </p>
                    <p>
                        Our collection includes:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-400 ml-4">
                        <li>Complete film collection on Blu-ray and digital</li>
                        <li>Video games spanning multiple platforms and generations</li>
                        <li>Official apparel, including t-shirts and accessories</li>
                        <li>Collectible figures, props, and memorabilia</li>
                        <li>Exclusive items available only through our store</li>
                    </ul>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 mt-8">
                    <a href="{{ route('movies') }}" class="font-cinzel px-6 py-2.5 bg-red-900/50 hover:bg-red-900 text-white uppercase tracking-widest text-sm font-bold transition-all border border-red-700 rounded">
                        Shop Movies
                    </a>
                    <a href="{{ route('games') }}" class="font-cinzel px-6 py-2.5 bg-red-900/50 hover:bg-red-900 text-white uppercase tracking-widest text-sm font-bold transition-all border border-red-700 rounded">
                        Shop Games
                    </a>
                    <a href="{{ route('merch') }}" class="font-cinzel px-6 py-2.5 bg-red-900/50 hover:bg-red-900 text-white uppercase tracking-widest text-sm font-bold transition-all border border-red-700 rounded">
                        Shop Merch
                    </a>
                </div>
            </div>

            <!-- Warning Quote -->
            <div class="text-center mb-16">
                <blockquote class="relative">
                    <svg class="absolute -top-4 -left-2 w-8 h-8 text-red-900/50" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                    <p class="font-cinzel text-xl md:text-2xl text-[#D8C9AE] italic max-w-2xl mx-auto px-8">
                        "You see, Jason was my son, and today is his birthday..."
                    </p>
                    <footer class="font-cinzel text-sm text-red-700 mt-4">
                        - Pamela Voorhees, Friday the 13th (1980)
                    </footer>
                </blockquote>
            </div>

        </main>

    </div>

    <div class="relative z-50">
        @include('partials.footer')
    </div>

</body>
</html>
