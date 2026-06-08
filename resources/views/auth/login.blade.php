@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg">
        <div class="text-center">
            <div class="mx-auto h-12 w-12 gradient-bg rounded-xl flex items-center justify-center">
                <i class="fas fa-hand-holding-heart text-white text-2xl"></i>
            </div>
            <h2 class="mt-6 text-3xl font-bold">Sign In</h2>
        </div>
        
        <form method="POST" action="/login" class="mt-8">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <button type="submit" class="w-full gradient-bg text-white py-2 rounded-lg font-semibold">
                Login
            </button>
        </form>
        
        <p class="text-center mt-4">Don't have an account? <a href="/register" class="text-indigo-600">Register</a></p>
    </div>
</div>
@endsection