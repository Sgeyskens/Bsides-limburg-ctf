<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Edit Profile - Friday the 13th</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white overflow-x-hidden">

    <!-- Hero Background Section - Only at top -->
    <div class="absolute top-0 left-0 right-0 h-screen z-0">
        <!-- Red Sky Background -->
        <img src="{{ asset('images/hero-sky.png') }}" alt="Red Sky Background" class="absolute inset-0 w-full h-full object-cover object-center">

        <!-- Mountains Silhouette Layer -->
        <img src="{{ asset('images/mountains.png') }}" alt="Mountains" class="hidden md:block absolute bottom-0 left-0 w-full h-auto">

        <!-- Watchtower -->
        <img src="{{ asset('images/watchtower.png') }}" alt="Watchtower" class="absolute bottom-0 right-[50px] lg:right-[150px] h-40 lg:h-70 w-auto object-contain hidden md:block">

        <!-- Gradient fade to black at bottom -->
        <div class="absolute bottom-0 left-0 right-0 h-96 bg-gradient-to-b from-transparent to-black"></div>
    </div>

    <!-- Content Wrapper -->
    <div class="relative z-10">
        <div class="px-4 md:px-8 lg:px-16 py-4 max-w-7xl w-full mx-auto">
            @include('partials.navbar')
        </div>

        <!-- Error/Success Messages -->
        @if(session('success'))
            <div class="max-w-2xl mx-auto px-4 md:px-8 mt-4">
                <div class="bg-green-900/30 border border-green-700 text-green-400 px-6 py-4 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-2xl mx-auto px-4 md:px-8 mt-4">
                <div class="bg-red-900/30 border border-red-700 text-red-400 px-6 py-4 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Edit Profile Page -->
        <div class="min-h-screen py-12 px-4 md:px-8">
            <div class="max-w-2xl mx-auto">
                <!-- Page Title -->
                <h1 class="font-cinzel text-4xl md:text-5xl text-center mb-12 uppercase tracking-wider text-[#D8C9AE]">
                    Edit Profile
                </h1>

                <!-- Edit Form Card -->
                <div class="bg-gradient-to-br from-red-950/30 via-black/80 to-red-950/30 border-2 border-red-900/50 rounded-lg overflow-hidden backdrop-blur-sm shadow-2xl shadow-red-900/20 p-8 md:p-12">

                    <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Preview & Upload -->
                        <div class="flex flex-col items-center mb-8">
                            <div class="relative group mb-4">
                                <div class="w-32 h-32 rounded-full border-4 border-red-900/50 overflow-hidden bg-black/60 flex items-center justify-center shadow-lg shadow-red-900/30">
                                    <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Profile" class="w-24 h-24 object-contain opacity-80">
                                </div>
                            </div>

                            <!-- File Upload -->
                            <label for="avatar" class="cursor-pointer bg-red-900/50 hover:bg-red-900 text-white font-cinzel px-4 py-2 rounded border-2 border-red-700 transition-all hover:scale-105 uppercase text-xs tracking-wider">
                                Change Picture
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewImage(this)">
                            </label>
                            <p class="text-gray-500 text-xs mt-2 font-cinzel">JPG, PNG or GIF (max 2MB)</p>
                        </div>

                        <script>
                            function previewImage(input) {
                                if (input.files && input.files[0]) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        document.getElementById('avatar-preview').src = e.target.result;
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                }
                            }
                        </script>

                        <!-- Username -->
                        <div class="mb-6">
                            <label for="username" class="block font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wider mb-2">
                                Username
                            </label>
                            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                                class="w-full bg-black/60 border-2 border-red-900/50 rounded px-4 py-3 text-white font-cinzel focus:outline-none focus:border-red-700 transition-colors">
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="block font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wider mb-2">
                                Email
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full bg-black/60 border-2 border-red-900/50 rounded px-4 py-3 text-white font-cinzel focus:outline-none focus:border-red-700 transition-colors">
                        </div>

                        <!-- Bio -->
                        <div class="mb-6">
                            <label for="bio" class="block font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wider mb-2">
                                Bio
                            </label>
                            <textarea id="bio" name="bio" rows="4" maxlength="500" placeholder="Tell us about yourself..."
                                class="w-full bg-black/60 border-2 border-red-900/50 rounded px-4 py-3 text-white font-cinzel focus:outline-none focus:border-red-700 transition-colors resize-none">{{ old('bio', $user->bio) }}</textarea>
                            <p class="text-gray-500 text-xs mt-1 font-cinzel">Max 500 characters</p>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-red-900/50 my-8"></div>
                        <p class="text-gray-400 text-sm mb-6 font-cinzel">Leave password fields empty to keep current password</p>

                        <!-- Current Password -->
                        <div class="mb-6">
                            <label for="current_password" class="block font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wider mb-2">
                                Current Password
                            </label>
                            <input type="password" id="current_password" name="current_password"
                                class="w-full bg-black/60 border-2 border-red-900/50 rounded px-4 py-3 text-white font-cinzel focus:outline-none focus:border-red-700 transition-colors">
                        </div>

                        <!-- New Password -->
                        <div class="mb-6">
                            <label for="password" class="block font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wider mb-2">
                                New Password
                            </label>
                            <input type="password" id="password" name="password"
                                class="w-full bg-black/60 border-2 border-red-900/50 rounded px-4 py-3 text-white font-cinzel focus:outline-none focus:border-red-700 transition-colors">
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-8">
                            <label for="password_confirmation" class="block font-cinzel text-[#D8C9AE] text-sm uppercase tracking-wider mb-2">
                                Confirm New Password
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full bg-black/60 border-2 border-red-900/50 rounded px-4 py-3 text-white font-cinzel focus:outline-none focus:border-red-700 transition-colors">
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" class="flex-1 bg-red-900 hover:bg-red-800 text-white font-cinzel px-6 py-3 rounded border-2 border-red-700 transition-all hover:scale-105 uppercase text-sm tracking-wider">
                                Save Changes
                            </button>
                            <a href="{{ route('account') }}" class="flex-1 bg-transparent hover:bg-red-900/30 text-red-700 font-cinzel px-6 py-3 rounded border-2 border-red-700 transition-all hover:scale-105 uppercase text-sm tracking-wider text-center">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('partials.footer', ['transparentFooter' => true])
    </div>
</body>
</html>
