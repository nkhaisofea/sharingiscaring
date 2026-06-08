<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EquipmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        $query = Equipment::with(['club', 'category'])->where('availability_status', 'available');
        
        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Sort
        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'price_low':
                $query->orderBy('price_per_day', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_per_day', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $equipment = $query->paginate(12);
        $categories = Category::all();
        
        return view('equipment.index', compact('equipment', 'categories'));
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['club', 'category']);

        $blockedRentals = $equipment->blockedRentals()
            ->with('borrower:id,name')
            ->get(['id', 'borrower_id', 'start_date', 'end_date', 'status']);

        $blockedDates = $equipment->getBlockedDates();
        
        $relatedEquipment = Equipment::with('category')
            ->where('category_id', $equipment->category_id)
            ->where('id', '!=', $equipment->id)
            ->where('availability_status', 'available')
            ->limit(4)
            ->get();
            
        return view('equipment.show', compact('equipment', 'relatedEquipment', 'blockedRentals', 'blockedDates'));
    }

    public function create()
    {
        if (!auth()->user()->isClubAdmin() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Only club admins can add equipment.');
        }
        
        $categories = Category::all();
        return view('equipment.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
            'condition' => 'required|in:new,excellent,good,fair,poor',
            'pickup_location' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipment', 'public');
            $validated['image'] = $path;
        }
        
        $validated['club_id'] = auth()->id();
        $validated['availability_status'] = 'available';
        
        Equipment::create($validated);
        
        return redirect()->route('dashboard')->with('success', 'Equipment added successfully!');
    }

    public function edit(Equipment $equipment)
    {
        if (auth()->id() !== $equipment->club_id && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }
        
        $categories = Category::all();
        return view('equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        if (auth()->id() !== $equipment->club_id && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric|min:0',
            'condition' => 'required|in:new,excellent,good,fair,poor',
            'availability_status' => 'required|in:available,rented,maintenance',
            'pickup_location' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('equipment', 'public');
            $validated['image'] = $path;
        }
        
        $equipment->update($validated);
        
        return redirect()->route('dashboard')->with('success', 'Equipment updated successfully!');
    }

    public function destroy(Equipment $equipment)
    {
        if (auth()->id() !== $equipment->club_id && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized action.');
        }
        
        $equipment->delete();
        
        return redirect()->route('dashboard')->with('success', 'Equipment deleted successfully!');
    }
}