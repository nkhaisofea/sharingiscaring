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
        
        if ($user->isClubAdmin() || $user->isSuperAdmin()) {
            // Admin Dashboard
            $equipment = Equipment::where('club_id', $user->id)->get();
            $totalEquipment = $equipment->count();
            $availableEquipment = $equipment->where('availability_status', 'available')->count();
            
            $pendingRequests = Rental::whereHas('equipment', function($q) use ($user) {
                $q->where('club_id', $user->id);
            })->where('status', 'pending')->count();
            
            $activeRentals = Rental::whereHas('equipment', function($q) use ($user) {
                $q->where('club_id', $user->id);
            })->where('status', 'approved')->count();
            
            $recentRequests = Rental::with(['equipment', 'borrower'])
                ->whereHas('equipment', function($q) use ($user) {
                    $q->where('club_id', $user->id);
                })
                ->latest()
                ->limit(5)
                ->get();
                
            $userEquipment = $equipment;
            
            return view('dashboard.admin', compact(
                'totalEquipment', 'availableEquipment', 'pendingRequests',
                'activeRentals', 'recentRequests', 'userEquipment'
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
