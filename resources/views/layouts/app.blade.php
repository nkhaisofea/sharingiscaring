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
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="gradient-bg w-10 h-10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hand-holding-heart text-white text-xl"></i>
                        </div>
                        <span class="font-bold text-xl bg-gradient-to-r from-indigo-600 to-pink-600 bg-clip-text text-transparent">
                            SharingIsCaring
                        </span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                    <a href="/equipment" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                        <i class="fas fa-microphone-alt mr-1"></i> Equipment
                    </a>
                    
                    @auth
                        <a href="/dashboard" class="text-gray-700 hover:text-indigo-600 transition font-medium">
                            <i class="fas fa-chart-line mr-1"></i> Dashboard
                        </a>
                        @if(auth()->user()->isClubAdmin())
                            <a href="/equipment/create" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition">
                                <i class="fas fa-plus mr-1"></i> Add Equipment
                            </a>
                        @endif
                    @endauth
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative group">
                            <button class="flex items-center space-x-2 focus:outline-none">
                                <div class="gradient-bg w-9 h-9 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ auth()->user()->getInitialsAttribute() }}
                                </div>
                                <span class="text-gray-700 hidden md:inline">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:inline"></i>
                            </button>
                            
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl hidden group-hover:block z-50">
                                <a href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <a href="/my-rentals" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fas fa-calendar-alt mr-2"></i> My Rentals
                                </a>
                                @if(auth()->user()->isClubAdmin())
                                    <a href="/pending-requests" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-clock mr-2"></i> Pending Requests
                                    </a>
                                @endif
                                <hr class="my-1">
                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="/login" class="text-gray-700 hover:text-indigo-600">Login</a>
                        <a href="/register" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition">
                            Register
                        </a>
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
</body>
</html>