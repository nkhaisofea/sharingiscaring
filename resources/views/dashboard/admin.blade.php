@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showRejectModal: false, rejectAction: '', rejectNotes: '' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Club Admin Dashboard</h1>
            <p class="text-gray-500 mt-1 font-medium">Manage equipment listings and incoming rental requests for <span class="text-indigo-600 font-semibold">{{ auth()->user()->club_name ?? 'your club' }}</span></p>
        </div>
        <a href="{{ route('equipment.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-indigo-100 transition duration-200 flex items-center gap-2">
            <i class="fas fa-plus"></i> Add New Equipment
        </a>
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
        <!-- Stat 3 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Pending Requests</span>
                <span class="text-3xl font-black text-amber-600 mt-1 block">{{ $pendingRequests }}</span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <!-- Stat 4 -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Active Rentals</span>
                <span class="text-3xl font-black text-indigo-600 mt-1 block">{{ $activeRentals }}</span>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fas fa-handshake"></i>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="space-y-10">
        <!-- Section: Recent Rental Requests -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-pull text-indigo-500"></i>
                    Recent Rental Requests
                </h2>
                <a href="{{ route('rentals.pending') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition">View All Pending</a>
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
                            <th class="px-6 py-4 text-center">Status / Actions</th>
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
                                    @if($rental->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <form method="POST" action="{{ route('rentals.approve', $rental) }}">
                                                @csrf
                                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm hover:shadow transition">
                                                    Approve
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    @click="showRejectModal = true; rejectAction = '{{ route('rentals.reject', $rental) }}'"
                                                    class="bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm hover:shadow transition">
                                                Reject
                                            </button>
                                        </div>
                                    @else
                                        <div class="flex justify-center">
                                            @php
                                                $badges = [
                                                    'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                    'completed' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                    'cancelled' => 'bg-gray-50 text-gray-600 border-gray-200'
                                                ];
                                            @endphp
                                            <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $badges[$rental->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                                {{ ucfirst($rental->status) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">
                                    <i class="fas fa-calendar-xmark text-gray-300 text-4xl mb-3 block"></i>
                                    No incoming rental requests yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section: My Equipment Listings -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-boxes-stacked text-indigo-500"></i>
                    My Equipment Listings
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-4">Image &amp; Name</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Price / Day</th>
                            <th class="px-6 py-4">Condition</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($userEquipment as $item)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex items-center justify-center shrink-0">
                                            @if($item->image)
                                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full gradient-bg flex items-center justify-center text-white">
                                                    <i class="fas {{ $item->category->icon ?? 'fa-box-open' }} text-sm"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('equipment.show', $item) }}" class="font-bold text-gray-900 hover:text-indigo-600 transition">{{ $item->name }}</a>
                                            <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $item->pickup_location }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-md">
                                        {{ $item->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900 text-sm">
                                    RM {{ number_format($item->price_per_day, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold capitalize text-gray-700">
                                    {{ $item->condition }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statuses = [
                                            'available' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'rented' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            'maintenance' => 'bg-amber-50 text-amber-700 border-amber-100'
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $statuses[$item->availability_status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                        {{ ucfirst($item->availability_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('equipment.edit', $item) }}" class="border border-gray-200 hover:border-indigo-600 hover:text-indigo-600 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('equipment.destroy', $item) }}" @submit.prevent="if(confirm('Are you sure you want to delete this equipment listing? This cannot be undone.')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="border border-gray-200 hover:border-rose-600 hover:text-rose-600 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">
                                    <i class="fas fa-boxes-packing text-gray-300 text-4xl mb-3 block"></i>
                                    You have not added any equipment yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rejection Notes Modal -->
    <x-modal show="showRejectModal" title="Reject Rental Request" icon="fa-exclamation-circle" icon-class="text-rose-500">
        <form method="POST" :action="rejectAction" class="p-6">
            @csrf
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Reason for Rejection</label>
                <textarea name="notes"
                          rows="3"
                          required
                          placeholder="Please explain why the request is rejected (e.g. equipment reserved, club events, maintenance)..."
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm placeholder-gray-400"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button"
                        @click="showRejectModal = false"
                        class="flex-1 py-3 border border-gray-200 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition active:scale-[0.98] text-sm">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-rose-500 hover:bg-rose-600 text-white py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-rose-100 active:scale-[0.98] transition duration-200 text-sm">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
