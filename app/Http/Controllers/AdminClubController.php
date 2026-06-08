<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminClubController extends Controller
{
    private function ensureSuperAdmin()
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $clubs = User::query()
            ->whereNotNull('club_name')
            ->whereIn('role', ['club_admin', 'pending_club'])
            ->withCount([
                'equipment',
                'clubRentals as active_rentals_count' => function ($query) {
                    $query->where('rentals.status', 'approved');
                },
            ])
            ->latest()
            ->paginate(15);

        return view('admin.clubs', compact('clubs'));
    }

    public function approve(User $user)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $user->update([
            'role' => 'club_admin',
            'club_status' => 'approved',
            'rejection_reason' => null,
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Club approved successfully.');
    }

    public function reject(Request $request, User $user)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $user->update([
            'role' => 'pending_club',
            'club_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Club rejected.');
    }

    public function suspend(Request $request, User $user)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'role' => 'club_admin',
            'club_status' => 'suspended',
            'rejection_reason' => $validated['rejection_reason'] ?? 'Suspended by super admin.',
            'suspended_at' => now(),
        ]);

        return back()->with('success', 'Club suspended.');
    }

    public function activate(User $user)
    {
        if ($redirect = $this->ensureSuperAdmin()) {
            return $redirect;
        }

        $user->update([
            'role' => 'club_admin',
            'club_status' => 'approved',
            'rejection_reason' => null,
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Club activated.');
    }
}
