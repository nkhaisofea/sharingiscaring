@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900 font-medium">Profile</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: IIUM ID Card Preview (Rich Aesthetic Visual) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 block text-center">IIUM Digital Badge</span>
                
                <!-- ID Card Container -->
                <div class="w-full max-w-[280px] bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-900 rounded-2xl shadow-xl overflow-hidden relative border border-indigo-950 flex flex-col justify-between min-h-[380px] text-white p-6">
                    <!-- Top Section -->
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-hand-holding-heart text-indigo-400 text-lg"></i>
                            <span class="text-xs font-black tracking-widest text-indigo-200">SIC SYSTEM</span>
                        </div>
                        <span class="text-[9px] bg-indigo-500/30 text-indigo-300 font-bold px-2 py-0.5 rounded-full border border-indigo-400/20 uppercase">IIUM</span>
                    </div>

                    <!-- Middle: Avatar & Basic Info -->
                    <div class="flex flex-col items-center my-6">
                        <div class="w-20 h-20 rounded-full border-2 border-indigo-400 bg-white text-indigo-900 font-black text-2xl flex items-center justify-center shadow-lg mb-3">
                            {{ $user->initials }}
                        </div>
                        <h2 class="font-bold text-lg text-center tracking-tight truncate w-full">{{ $user->name }}</h2>
                        <span class="text-[10px] text-indigo-300 font-semibold uppercase tracking-widest mt-0.5">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>

                    <!-- Bottom Info -->
                    <div class="border-t border-indigo-800/50 pt-4 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-indigo-400/80 font-medium text-[10px] uppercase">Student ID</span>
                            <span class="font-bold text-gray-100">{{ $user->student_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-indigo-400/80 font-medium text-[10px] uppercase">Email</span>
                            <span class="font-bold text-gray-100 text-right truncate max-w-[150px]">{{ $user->email }}</span>
                        </div>
                        @if($user->club_name)
                            <div class="flex justify-between">
                                <span class="text-indigo-400/80 font-medium text-[10px] uppercase">Club</span>
                                <span class="font-bold text-indigo-300 text-right truncate max-w-[150px]">{{ $user->club_name }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Decorative Chip / NFC Indicator -->
                    <div class="absolute right-4 top-16 w-8 h-6 bg-amber-400/20 border border-amber-400/40 rounded-md flex items-center justify-center opacity-60">
                        <i class="fas fa-microchip text-amber-400 text-xs"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 gradient-bg text-white rounded-xl flex items-center justify-center text-lg shadow-md shadow-indigo-100">
                        <i class="fas fa-user-gear"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Account Settings</h1>
                        <p class="text-sm text-gray-500 font-medium">Update your display name and change account password.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-4 py-3 border @error('name') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Section Title -->
                    <div class="pt-4 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-lock text-indigo-500"></i>
                            Change Password
                        </h3>
                        <p class="text-xs text-gray-400 font-medium">Leave password fields blank if you do not wish to change your password.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">New Password</label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   placeholder="Min. 8 characters"
                                   class="w-full px-4 py-3 border @error('password') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition">
                            @error('password')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Confirm New Password</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   placeholder="Re-enter password"
                                   class="w-full px-4 py-3 border border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 rounded-xl focus:outline-none focus:ring-2 text-sm transition">
                        </div>
                    </div>

                    <!-- Submit Controls -->
                    <div class="flex gap-4 pt-4 border-t border-gray-100 justify-end">
                        <button type="submit" 
                                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3.5 rounded-xl font-bold hover:shadow-lg hover:shadow-indigo-100 active:scale-[0.99] transition duration-200 text-sm">
                            Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
