<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RentalController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function store(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string|max:500'
        ]);
        
        // Calculate total price
        $days = (strtotime($validated['end_date']) - strtotime($validated['start_date'])) / (60 * 60 * 24) + 1;
        $total_price = $days * $equipment->price_per_day;
        
        Rental::create([
            'equipment_id' => $equipment->id,
            'borrower_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'purpose' => $validated['purpose'],
            'total_price' => $total_price,
            'status' => 'pending'
        ]);
        
        return redirect()->route('rentals.my-rentals')->with('success', 'Rental request submitted successfully!');
    }

    public function myRentals()
    {
        $rentals = Rental::with(['equipment', 'equipment.club'])
            ->where('borrower_id', auth()->id())
            ->latest()
            ->paginate(10);
            
        return view('rentals.my-rentals', compact('rentals'));
    }

    public function pendingRequests()
    {
        if (!auth()->user()->isClubAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        $requests = Rental::with(['equipment', 'borrower'])
            ->whereHas('equipment', function($q) {
                $q->where('club_id', auth()->id());
            })
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
            
        return view('rentals.pending-requests', compact('requests'));
    }

    public function approve(Rental $rental)
    {
        if ($rental->equipment->club_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        DB::transaction(function () use ($rental) {
            $rental->update(['status' => 'approved']);
            $rental->equipment->update(['availability_status' => 'rented']);
        });
        
        return redirect()->back()->with('success', 'Rental request approved!');
    }

    public function reject(Request $request, Rental $rental)
    {
        if ($rental->equipment->club_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        $rental->update([
            'status' => 'rejected',
            'admin_notes' => $request->notes
        ]);
        
        return redirect()->back()->with('success', 'Rental request rejected.');
    }

    public function complete(Rental $rental)
    {
        if ($rental->equipment->club_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        DB::transaction(function () use ($rental) {
            $rental->update(['status' => 'completed']);
            $rental->equipment->update(['availability_status' => 'available']);
        });
        
        return redirect()->back()->with('success', 'Rental marked as completed.');
    }

    public function cancel(Rental $rental)
    {
        if ($rental->borrower_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        
        if ($rental->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be cancelled.');
        }
        
        $rental->update(['status' => 'cancelled']);
        
        return redirect()->back()->with('success', 'Rental request cancelled.');
    }
}