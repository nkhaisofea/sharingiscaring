<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SharingIsCaring - @yield('title', 'IIUM Club Equipment Rental')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        [x-cloak] { display: none !important; }

        .flatpickr-day.disabled,
        .flatpickr-day.disabled:hover {
            background: #ffe4e6 !important;
            border-color: #fecdd3 !important;
            color: #e11d48 !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    
    <!-- Navigation Bar -->
    @php
        $pendingClubApprovalsCount = auth()->check() && auth()->user()->isSuperAdmin()
            ? \App\Models\User::where('role', 'pending_club')->count()
            : 0;
    @endphp
    <nav class="sticky top-0 z-50 border-b border-gray-100 bg-white shadow-sm" x-data="{ userMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid h-16 grid-cols-[auto,1fr,auto] items-center gap-4">
                <div class="flex min-w-0 items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="gradient-bg flex h-10 w-10 shrink-0 items-center justify-center rounded-xl">
                            <i class="fas fa-hand-holding-heart text-white text-xl"></i>
                        </div>
                        <span class="hidden text-xl font-bold bg-gradient-to-r from-indigo-600 to-pink-600 bg-clip-text text-transparent sm:inline">
                            SharingIsCaring
                        </span>
                    </a>
                </div>
                
                <div class="hidden min-w-0 items-center justify-center gap-5 lg:flex">
                    <a href="/" class="inline-flex h-10 items-center whitespace-nowrap text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                    <a href="/equipment" class="inline-flex h-10 items-center whitespace-nowrap text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
                        <i class="fas fa-microphone-alt mr-1"></i> Equipment
                    </a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center whitespace-nowrap text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
                            <i class="fas fa-chart-line mr-1"></i> Dashboard
                        </a>
                        @if(auth()->user()->role === 'member' || auth()->user()->isSuperAdmin())
                            <a href="{{ route('rentals.my-rentals') }}" class="inline-flex h-10 items-center whitespace-nowrap text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
                                <i class="fas fa-calendar-alt mr-1"></i> My Rentals
                            </a>
                        @endif
                        @if(auth()->user()->isClubAdmin() || auth()->user()->isSuperAdmin())
                            @if(auth()->user()->isSuperAdmin())
                                <a href="{{ route('admin.clubs.index') }}" class="inline-flex h-10 items-center whitespace-nowrap text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
                                    <i class="fas fa-users-gear mr-1"></i> Manage Clubs
                                </a>
                                <a href="{{ route('admin.pending-clubs') }}" class="inline-flex h-10 items-center whitespace-nowrap text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
                                    <i class="fas fa-user-check mr-1"></i> Club Approvals
                                    @if($pendingClubApprovalsCount > 0)
                                        <span class="ml-1 inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 py-0.5 text-xs font-bold leading-none text-white">
                                            {{ $pendingClubApprovalsCount }}
                                        </span>
                                    @endif
                                </a>
                            @endif
                            <a href="{{ route('equipment.create') }}" class="inline-flex h-10 items-center whitespace-nowrap text-sm font-semibold text-gray-700 transition hover:text-indigo-600">
                                <i class="fas fa-plus mr-1"></i> Add Equipment
                            </a>
                        @endif
                    @endauth
                </div>
                
                <div class="flex items-center justify-end">
                    @auth
                        <div class="relative" @click.away="userMenuOpen = false">
                            <button type="button"
                                    class="flex h-11 items-center space-x-2 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    @click="userMenuOpen = !userMenuOpen"
                                    :aria-expanded="userMenuOpen.toString()">
                                <div class="gradient-bg flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white">
                                    {{ auth()->user()->getInitialsAttribute() }}
                                </div>
                                <span class="hidden max-w-40 truncate text-sm font-semibold text-gray-700 md:inline">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:inline transition"
                                   :class="userMenuOpen ? 'rotate-180' : ''"></i>
                            </button>
                            
                            <div x-show="userMenuOpen"
                                 x-cloak
                                 x-transition
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fas fa-chart-line mr-2"></i> Dashboard
                                </a>
                                @if(auth()->user()->role === 'member' || auth()->user()->isSuperAdmin())
                                    <a href="{{ route('rentals.my-rentals') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-calendar-alt mr-2"></i> My Rentals
                                    </a>
                                @endif
                                @if(auth()->user()->isClubAdmin() || auth()->user()->isSuperAdmin())
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('admin.clubs.index') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                            <i class="fas fa-users-gear mr-2"></i> Manage Clubs
                                        </a>
                                        <a href="{{ route('admin.pending-clubs') }}" class="flex items-center justify-between px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                            <span><i class="fas fa-user-check mr-2"></i> Club Approvals</span>
                                            @if($pendingClubApprovalsCount > 0)
                                                <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 py-0.5 text-xs font-bold leading-none text-white">
                                                    {{ $pendingClubApprovalsCount }}
                                                </span>
                                            @endif
                                        </a>
                                    @endif
                                    <a href="{{ route('equipment.create') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-plus mr-2"></i> Add Equipment
                                    </a>
                                @endif
                                <a href="{{ route('profile') }}" class="block px-4 py-2.5 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600">Login</a>
                            <a href="{{ route('register') }}" class="rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-bold text-white transition hover:shadow-lg">
                            Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="fixed top-20 right-4 z-50 animate-fadeIn" x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="fixed top-20 right-4 z-50 animate-fadeIn" x-data="{show: true}" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        </div>
    @endif
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
