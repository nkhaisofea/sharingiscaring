<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Rental;

use Illuminate\Routing\Controllers\HasMiddleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isPendingClub()) {
            return redirect()->route('login')
                ->with('error', 'Your club registration is still pending approval.');
        }

        if ($user->isRejectedClub()) {
            return redirect()->route('login')
                ->with('error', 'Your club registration was rejected.');
        }

        if ($user->isSuspendedClub()) {
            return redirect()->route('login')
                ->with('error', 'Your club account is currently suspended.');
        }
        
        if ($user->isClubAdmin() || $user->isSuperAdmin()) {
            // Super admins can manage all club equipment. Club admins only see their own club equipment.
            $equipmentQuery = Equipment::query();

            if ($user->isClubAdmin()) {
                $equipmentQuery->where('club_id', $user->id);
            }

            $equipment = $equipmentQuery->get();
            $totalEquipment = $equipment->count();
            $availableEquipment = $equipment->where('availability_status', 'available')->count();
            $rentedEquipment = $equipment->where('availability_status', 'rented')->count();
            $maintenanceEquipment = $equipment->where('availability_status', 'maintenance')->count();

            $adminRentalQuery = Rental::query();

            if ($user->isClubAdmin()) {
                $adminRentalQuery->whereHas('equipment', function($q) use ($user) {
                    $q->where('club_id', $user->id);
                });
            }

            $pendingRequests = (clone $adminRentalQuery)
    ->where('status', 'pending')
    ->count();
            $activeRentals = (clone $adminRentalQuery)->where('status', 'approved')->count();

            $recentRequests = Rental::with(['equipment', 'borrower'])
    ->when($user->isClubAdmin(), function($query) use ($user) {
        $query->whereHas('equipment', function($q) use ($user) {
            $q->where('club_id', $user->id);
        });
    })
->whereIn('status', ['pending', 'approved'])
    ->latest()
    ->limit(5)
    ->get();
            return view('dashboard.admin', compact(
                'totalEquipment', 'availableEquipment', 'rentedEquipment',
                'maintenanceEquipment', 'pendingRequests', 'activeRentals', 'recentRequests'
            ));
        }
        
        // Member Dashboard
        $activeRentals = Rental::with('equipment.club')
            ->where('borrower_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->get();
            
        $rentalHistory = Rental::with('equipment')
            ->where('borrower_id', $user->id)
            ->whereIn('status', ['completed', 'rejected', 'cancelled'])
            ->latest()
            ->limit(5)
            ->get();
            
        return view('dashboard.member', compact('activeRentals', 'rentalHistory'));
    }
}
