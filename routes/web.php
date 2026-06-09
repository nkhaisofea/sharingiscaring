<?php

use App\Http\Controllers\AdminClubController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\RentalController;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    $featuredEquipment = Equipment::with(['club', 'category'])
        ->inRandomOrder()
        ->limit(4)
        ->get();

    $stats = [
        'clubs' => User::whereNotNull('club_name')->count(),
        'equipment' => Equipment::count(),
        'rentals' => Rental::count(),
    ];

    return view('home', compact('featuredEquipment', 'stats'));
})->name('home');
Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');

// Guest routes (auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Super Admin Club Approvals
    Route::get('/admin/pending-clubs', [AuthController::class, 'pendingClubs'])->name('admin.pending-clubs');
    Route::post('/admin/pending-clubs/{user}/approve', [AuthController::class, 'approveClub'])->name('admin.pending-clubs.approve');
    Route::post('/admin/pending-clubs/{user}/reject', [AuthController::class, 'rejectClub'])->name('admin.pending-clubs.reject');
    Route::get('/admin/clubs', [AdminClubController::class, 'index'])->name('admin.clubs.index');
    Route::post('/admin/clubs/{user}/approve', [AdminClubController::class, 'approve'])->name('admin.clubs.approve');
    Route::post('/admin/clubs/{user}/reject', [AdminClubController::class, 'reject'])->name('admin.clubs.reject');
    Route::post('/admin/clubs/{user}/suspend', [AdminClubController::class, 'suspend'])->name('admin.clubs.suspend');
    Route::post('/admin/clubs/{user}/activate', [AdminClubController::class, 'activate'])->name('admin.clubs.activate');
    
    // Equipment Management (CRUD)
    Route::resource('equipment', EquipmentController::class)->except(['index', 'show']);
    
    // Rentals
    Route::post('/equipment/{equipment}/rent', [RentalController::class, 'store'])->name('rentals.store');
    Route::get('/my-rentals', [RentalController::class, 'myRentals'])->name('rentals.my-rentals');
    Route::post('/rentals/{rental}/complete', [RentalController::class, 'complete'])->name('rentals.complete');
    Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel'])->name('rentals.cancel');
});

// Fallback wildcard route for single equipment view
Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
