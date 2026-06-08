@extends('layouts.app')

@section('title', 'My Rentals')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900 font-medium">My Rentals</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 gradient-bg text-white rounded-xl flex items-center justify-center text-lg shadow-md shadow-indigo-100">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">My Rental Requests</h1>
                    <p class="text-sm text-gray-500 font-medium">View the status and details of your club equipment rentals.</p>
                </div>
            </div>
            <a href="{{ route('equipment.index') }}" class="bg-indigo-600 text-white font-bold px-4 py-2 rounded-xl text-xs hover:bg-indigo-700 transition">
                New Request
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4">Equipment</th>
                        <th class="px-6 py-4">Managed By</th>
                        <th class="px-6 py-4">Rental Period</th>
                        <th class="px-6 py-4">Total Price</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Purpose &amp; Admin Notes</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rentals as $rental)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('equipment.show', $rental->equipment) }}" class="font-bold text-indigo-600 hover:underline block text-sm">{{ $rental->equipment->name }}</a>
                                <span class="text-[9px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-extrabold uppercase mt-0.5 inline-block">{{ $rental->equipment->category->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800 text-sm">
                                    {{ $rental->equipment->club->club_name ?? $rental->equipment->club->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-800">{{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400 font-medium">{{ $rental->duration_in_days }} Day(s)</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 text-sm">
                                RM {{ number_format($rental->total_price, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badges = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        'completed' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'cancelled' => 'bg-gray-50 text-gray-600 border-gray-200'
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $badges[$rental->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                    {{ ucfirst($rental->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs space-y-1 max-w-xs">
                                <div>
                                    <span class="font-bold text-gray-400 block uppercase tracking-wider text-[9px]">Purpose:</span>
                                    <span class="text-gray-600">{{ $rental->purpose }}</span>
                                </div>
                                @if($rental->admin_notes)
                                    <div class="pt-1.5 border-t border-gray-50">
                                        <span class="{{ $rental->status === 'rejected' ? 'text-rose-600' : 'text-indigo-600' }} font-bold block uppercase tracking-wider text-[9px]">Admin Remark:</span>
                                        <span class="text-gray-500 italic">"{{ $rental->admin_notes }}"</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($rental->status === 'pending')
                                    <form method="POST" action="{{ route('rentals.cancel', $rental) }}" @submit.prevent="if(confirm('Are you sure you want to cancel this rental request?')) $el.submit()">
                                        @csrf
                                        <button type="submit" class="border border-rose-100 text-rose-600 hover:bg-rose-50 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                            Cancel Request
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 font-semibold">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 font-medium">
                                <i class="fas fa-calendar-xmark text-gray-300 text-4xl mb-3 block"></i>
                                You have not submitted any rental requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rentals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $rentals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
