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
        if (auth()->id() === $equipment->club_id && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('equipment.show', $equipment)
                ->with('error', 'You cannot rent your own equipment.');
        }

        if (!$equipment->isAvailable()) {
            return redirect()->route('equipment.show', $equipment)
                ->with('error', 'This equipment is currently unavailable.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string|max:500'
        ]);

        if ($equipment->hasDateConflict($validated['start_date'], $validated['end_date'])) {
            return redirect()->route('equipment.show', $equipment)
                ->with('error', 'Selected dates conflict with an existing booking. Please choose different dates.')
                ->withInput();
        }
        
        // Calculate total price
        $days = (strtotime($validated['end_date']) - strtotime($validated['start_date'])) / (60 * 60 * 24) + 1;
        $total_price = $days * $equipment->price_per_day;
        
        DB::transaction(function () use ($equipment, $validated, $total_price) {
        Rental::create([
            'equipment_id' => $equipment->id,
            'borrower_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'purpose' => $validated['purpose'],
            'total_price' => $total_price,
            'status' => 'pending'
        ]);
        });
        
        return redirect()->route('rentals.my-rentals')->with('success', 'Rental confirmed successfully!');
    }

    public function myRentals()
    {
        $rentals = Rental::with(['equipment', 'equipment.club'])
            ->where('borrower_id', auth()->id())
            ->latest()
            ->paginate(10);
            
        return view('rentals.my-rentals', compact('rentals'));
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
    public function approve(Rental $rental)
{
    if (
        $rental->equipment->club_id !== auth()->id()
        && !auth()->user()->isSuperAdmin()
    ) {
        return back()->with('error', 'Unauthorized action.');
    }

    DB::transaction(function () use ($rental) {
        $rental->update([
            'status' => 'approved'
        ]);

        $rental->equipment->update([
            'availability_status' => 'rented'
        ]);
    });

    return back()->with('success', 'Rental approved.');
}
public function reject(Rental $rental)
{
    if (
        $rental->equipment->club_id !== auth()->id()
        && !auth()->user()->isSuperAdmin()
    ) {
        return back()->with('error', 'Unauthorized action.');
    }

    $rental->update([
        'status' => 'rejected'
    ]);

    return back()->with('success', 'Rental rejected.');
}
}
