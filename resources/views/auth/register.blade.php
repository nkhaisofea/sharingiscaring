@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
        <div class="text-center">
            <div class="mx-auto h-12 w-12 gradient-bg rounded-xl flex items-center justify-center">
                <i class="fas fa-hand-holding-heart text-white text-2xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-bold">Register</h2>
        </div>
        @if ($errors->any())
            <div class="mb-4 p-3.5 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-xs font-semibold flex items-start gap-2 animate-fadeIn">
                <i class="fas fa-circle-exclamation mt-0.5"></i>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="mt-8" x-data="{ role: '{{ old('role', 'member') }}' }">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium mb-2">Account Type</label>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                           :class="role === 'member' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100' : 'border-gray-200 bg-white hover:border-indigo-200'">
                        <input type="radio"
                               name="role"
                               value="member"
                               x-model="role"
                               class="mt-1 text-indigo-600 focus:ring-indigo-500">
                        <span>
                            <span class="block text-sm font-bold text-gray-900">Student Member</span>
                            <span class="mt-1 block text-xs text-gray-500">Use your IIUM student account.</span>
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition"
                           :class="role === 'club_admin' ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-100' : 'border-gray-200 bg-white hover:border-indigo-200'">
                        <input type="radio"
                               name="role"
                               value="club_admin"
                               x-model="role"
                               class="mt-1 text-indigo-600 focus:ring-indigo-500">
                        <span>
                            <span class="block text-sm font-bold text-gray-900">Club Admin</span>
                            <span class="mt-1 block text-xs text-gray-500">Register your club account.</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="mb-4" x-show="role === 'member'">
                <label class="block text-sm font-medium mb-2">Full Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       :required="role === 'member'"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4" x-show="role === 'club_admin'" x-cloak>
                <label class="block text-sm font-medium mb-2">Club Name</label>
                <input type="text"
                       name="club_name"
                       value="{{ old('club_name') }}"
                       :required="role === 'club_admin'"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="Example: IIUM Sports Club">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1" x-show="role === 'member'">Must end with @student.iium.edu.my</p>
                <p class="text-xs text-gray-500 mt-1" x-show="role === 'club_admin'" x-cloak>Email can be any valid email.</p>
            </div>
            <div class="mb-4" x-show="role === 'member'">
                <label class="block text-sm font-medium mb-2">Student ID</label>
                <input type="text"
                       name="student_id"
                       value="{{ old('student_id') }}"
                       :required="role === 'member'"
                       class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-gray-200 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="w-full gradient-bg text-white py-2 rounded-lg font-semibold">
                Register
            </button>
        </form>
        
        <p class="text-center mt-4">Already have an account? <a href="/login" class="text-indigo-600">Login</a></p>
    </div>
</div>
@endsection
