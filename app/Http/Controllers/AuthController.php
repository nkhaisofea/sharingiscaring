<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Show registration page
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration request
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|ends_with:@student.iium.edu.my,@iium.edu.my',
            'student_id' => 'required|string|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'student_id' => $validated['student_id'],
            'password' => Hash::make($validated['password']),
            'role' => 'member'
        ]);
        
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Welcome to SharingIsCaring!');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    // Show profile page
    public function profile()
    {
        $user = auth()->user();
        return view('auth.profile', compact('user'));
    }

    // Update profile
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:8|confirmed'
        ]);
        
        $user->name = $validated['name'];
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}