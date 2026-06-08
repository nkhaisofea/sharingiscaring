@extends('layouts.app')

@section('title', 'Pending Rental Requests')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showRejectModal: false, rejectAction: '' }">
    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900 font-medium">Pending Requests</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
            <div class="w-10 h-10 gradient-bg text-white rounded-xl flex items-center justify-center text-lg shadow-md shadow-indigo-100">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Pending Rental Requests</h1>
                <p class="text-sm text-gray-500 font-medium">Manage incoming equipment booking requests for your club.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4">Borrower Details</th>
                        <th class="px-6 py-4">Equipment</th>
                        <th class="px-6 py-4">Rental Period</th>
                        <th class="px-6 py-4">Total Price</th>
                        <th class="px-6 py-4">Purpose</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $rental)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $rental->borrower->name }}</div>
                                <div class="text-xs text-gray-400 font-medium">ID: {{ $rental->borrower->student_id }}</div>
                                <div class="text-xs text-gray-400 font-medium">Email: {{ $rental->borrower->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('equipment.show', $rental->equipment) }}" class="font-bold text-indigo-600 hover:underline block text-sm">{{ $rental->equipment->name }}</a>
                                <span class="text-[9px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-extrabold uppercase mt-0.5 inline-block">{{ $rental->equipment->category->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-800">{{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}</div>
                                <div class="text-xs text-gray-400 font-medium">{{ $rental->duration_in_days }} Day(s)</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 text-sm">
                                RM {{ number_format($rental->total_price, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-600 max-w-xs" style="word-break: break-word;">{{ $rental->purpose }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('rentals.approve', $rental) }}">
                                        @csrf
                                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-3.5 py-2 rounded-lg shadow-sm hover:shadow transition duration-200">
                                            Approve
                                        </button>
                                    </form>
                                    <button type="button" 
                                            @click="showRejectModal = true; rejectAction = '{{ route('rentals.reject', $rental) }}'"
                                            class="bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold px-3.5 py-2 rounded-lg shadow-sm hover:shadow transition duration-200">
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">
                                <i class="fas fa-check-circle text-emerald-400 text-4xl mb-3 block"></i>
                                All caught up! No pending requests.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $requests->links() }}
            </div>
        @endif
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
