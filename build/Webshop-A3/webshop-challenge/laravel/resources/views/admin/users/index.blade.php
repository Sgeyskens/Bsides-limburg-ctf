<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>Manage Users - Admin</title>
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
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.discounts.index') }}" class="font-cinzel text-sm text-gray-400 hover:text-red-700 transition-colors">
                        Discounts
                    </a>
                    <a href="{{ route('account') }}" class="font-cinzel text-sm text-[#D8C9AE] hover:text-red-700 transition-colors">
                        Back to Account
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 md:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="font-cinzel text-3xl md:text-4xl text-[#D8C9AE] uppercase tracking-wider font-bold">User Management</h2>
                <p class="font-cinzel text-sm text-gray-400 mt-1">View and manage user profiles</p>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-black/80 border-2 border-red-900/50 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-900/30 border-b border-red-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Joined</th>
                            <th class="px-4 py-3 text-left font-cinzel text-xs text-[#D8C9AE] uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-900/30">
                        @forelse($users as $user)
                            <tr class="hover:bg-red-900/10 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}" class="w-10 h-10 rounded-full object-cover border-2 border-red-900/50">
                                        <span class="font-cinzel text-white font-bold">{{ $user->username }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 font-cinzel text-gray-300">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-4">
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-cinzel uppercase tracking-wider bg-red-900/50 text-red-500 border border-red-700">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-cinzel uppercase tracking-wider bg-gray-900/50 text-gray-400 border border-gray-700">
                                            Survivor
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-cinzel text-gray-400 text-sm">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-4">
                                    <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center gap-2 px-3 py-1 text-[#D8C9AE] hover:text-white hover:bg-red-900/50 rounded transition-colors font-cinzel text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Profile
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <div class="font-cinzel text-gray-400">
                                        <p class="text-lg">No users found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
