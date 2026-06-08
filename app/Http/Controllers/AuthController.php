<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'role' => 'required|in:member,club_admin',
            'student_id' => 'required_if:role,member|nullable|string',
            'email' => 'required_if:role,club_admin|nullable|email',
            'password' => 'required',
        ]);

        $user = null;

        if ($validated['role'] === 'member') {
            $user = User::where('student_id', $validated['student_id'])
                ->where('role', 'member')
                ->first();
        } else {
            $user = User::where('email', $validated['email'])
                ->whereIn('role', ['club_admin', 'super_admin', 'pending_club'])
                ->first();

            if ($user && $user->isPendingClub()) {
                return back()->withErrors([
                    'email' => 'Your club registration is still pending approval.',
                ])->onlyInput('email');
            }

            if ($user && $user->isRejectedClub()) {
                return back()->withErrors([
                    'email' => 'Your club registration was rejected. Please contact the super admin for details.',
                ])->onlyInput('email');
            }

            if ($user && $user->isSuspendedClub()) {
                return back()->withErrors([
                    'email' => 'Your club account is currently suspended.',
                ])->onlyInput('email');
            }
        }

        if ($user && Hash::check($validated['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        $credentialField = $validated['role'] === 'member' ? 'student_id' : 'email';
        
        return back()->withErrors([
            $credentialField => 'The provided credentials do not match our records.',
        ])->onlyInput($credentialField);
    }

    // Show registration page
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle registration request
    public function register(Request $request)
    {
        $request->merge([
            'role' => $request->input('role', 'member'),
        ]);

        $validated = $request->validate([
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($request->input('role') === 'member'),
            ],
            'role' => 'required|in:member,club_admin',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                Rule::when($request->input('role') === 'member', ['ends_with:@student.iium.edu.my']),
            ],
            'student_id' => [
                'nullable',
                'string',
                'unique:users,student_id',
                Rule::requiredIf($request->input('role') === 'member'),
            ],
            'club_name' => [
                'nullable',
                'string',
                'max:255',
                'unique:users,club_name',
                Rule::requiredIf($request->input('role') === 'club_admin'),
            ],
            'password' => 'required|min:8|confirmed',
        ], [
            'club_name.unique' => 'This club name is already registered',
        ]);

        $isClubRegistration = $validated['role'] === 'club_admin';
        
        $user = User::create([
            'name' => $isClubRegistration ? $validated['club_name'] : $validated['name'],
            'email' => $validated['email'],
            'student_id' => $isClubRegistration ? null : $validated['student_id'],
            'password' => Hash::make($validated['password']),
            'role' => $isClubRegistration ? 'pending_club' : 'member',
            'club_name' => $isClubRegistration ? $validated['club_name'] : null,
            'club_status' => $isClubRegistration ? 'pending' : null,
        ]);
        
        if ($isClubRegistration) {
            return redirect()->route('login')
                ->with('success', 'Club registration submitted. Please wait for super admin approval before logging in.');
        }

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

    public function pendingClubs()
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $pendingClubs = User::where('role', 'pending_club')
            ->where(function ($query) {
                $query->whereNull('club_status')->orWhere('club_status', 'pending');
            })
            ->latest()
            ->paginate(10);

        return view('admin.pending-clubs', compact('pendingClubs'));
    }

    public function approveClub(User $user)
    {
        if (!auth()->user()->isSuperAdmin() || !$user->isPendingClub()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $user->update([
            'role' => 'club_admin',
            'club_status' => 'approved',
            'rejection_reason' => null,
            'suspended_at' => null,
        ]);

        return redirect()->back()->with('success', 'Club registration approved.');
    }

    public function rejectClub(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin() || !$user->isPendingClub()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'club_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? 'Rejected by super admin.',
            'suspended_at' => null,
        ]);

        return redirect()->back()->with('success', 'Club registration rejected.');
    }
}
