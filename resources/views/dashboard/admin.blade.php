@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8" x-data="{ showRejectModal: false, rejectAction: '', rejectNotes: '' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                {{ auth()->user()->isSuperAdmin() ? 'Super Admin Dashboard' : 'Club Admin Dashboard' }}
            </h1>
            <p class="text-gray-500 mt-1 font-medium">
                Review equipment stats and recent rental activity for
                <span class="text-indigo-600 font-semibold">{{ auth()->user()->isSuperAdmin() ? 'all clubs' : (auth()->user()->club_name ?? 'your club') }}</span>.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.clubs.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-3 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100">
                    <i class="fas fa-users-gear"></i> Manage Clubs
                </a>
            @endif
            <a href="{{ route('equipment.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-3 text-sm font-bold text-white transition hover:shadow-lg hover:shadow-indigo-100">
                <i class="fas fa-plus"></i> Add Equipment
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Stat 1 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Total Equipment</span>
                <span class="text-3xl font-black text-gray-900 mt-1 block">{{ $totalEquipment }}</span>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-boxes-stacked"></i>
            </div>
        </div>
        <!-- Stat 2 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Available</span>
                <span class="text-3xl font-black text-emerald-600 mt-1 block">{{ $availableEquipment }}</span>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Rented</span>
                <span class="text-3xl font-black text-blue-600 mt-1 block">{{ $rentedEquipment }}</span>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-handshake"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Maintenance</span>
                <span class="text-3xl font-black text-amber-600 mt-1 block">{{ $maintenanceEquipment }}</span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-screwdriver-wrench"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-10 md:grid-cols-2">
        <div class="rounded-2xl border border-amber-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Confirmed Today</span>
                    <span class="mt-1 block text-3xl font-black text-amber-600">{{ $pendingRequests }}</span>
                </div>
                <i class="fas fa-bolt text-amber-500"></i>
            </div>
        </div>
        <div class="rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Active Rentals</span>
                    <span class="mt-1 block text-3xl font-black text-indigo-600">{{ $activeRentals }}</span>
                </div>
                <i class="fas fa-calendar-check text-indigo-500"></i>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="space-y-10">
        <!-- Section: Recent Rental Requests -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-days text-indigo-500"></i>
                    Recent Active Rentals
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4">Borrower</th>
                            <th class="px-6 py-4">Equipment</th>
                            <th class="px-6 py-4">Rental Period</th>
                            <th class="px-6 py-4">Total Price</th>
                            <th class="px-6 py-4">Purpose</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentRequests as $rental)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $rental->borrower->name }}</div>
                                    <div class="text-xs text-gray-400 font-medium">ID: {{ $rental->borrower->student_id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('equipment.show', $rental->equipment) }}" class="font-bold text-indigo-600 hover:underline block">{{ $rental->equipment->name }}</a>
                                    <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-bold uppercase">{{ $rental->equipment->category->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-800">{{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400 font-medium">{{ $rental->duration_in_days }} Day(s)</div>
                                </td>
                                <td class="px-6 py-4 font-extrabold text-gray-900 text-sm">
                                    RM {{ number_format($rental->total_price, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-600 max-w-xs line-clamp-2" title="{{ $rental->purpose }}">{{ $rental->purpose }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        @php
                                            $badges = [
                                                'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                'completed' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                'cancelled' => 'bg-gray-50 text-gray-600 border-gray-200'
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $badges[$rental->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                            {{ ucfirst($rental->status) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">
                                    <i class="fas fa-calendar-xmark text-gray-300 text-4xl mb-3 block"></i>
                                    No active rentals yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
