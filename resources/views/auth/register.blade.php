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
        
        <form method="POST" action="/register" class="mt-8">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Full Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Student ID</label>
                <input type="text" name="student_id" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">IIUM Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Must end with @student.iium.edu.my or @iium.edu.my</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <button type="submit" class="w-full gradient-bg text-white py-2 rounded-lg font-semibold">
                Register
            </button>
        </form>
        
        <p class="text-center mt-4">Already have an account? <a href="/login" class="text-indigo-600">Login</a></p>
    </div>
</div>
@endsection