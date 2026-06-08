@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Member Dashboard</p>
            <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900">
                Welcome back, {{ auth()->user()->name }}
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Track your equipment requests, approved rentals, and past booking activity.
            </p>
        </div>

        <a href="{{ route('equipment.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 hover:shadow-md">
            <i class="fas fa-magnifying-glass"></i>
            Browse Equipment
        </a>
    </div>

    <section class="mb-10">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Active Rentals</h2>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                Approved Rentals
            </span>
        </div>

        @forelse($activeRentals as $rental)
            <div class="mb-4 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <a href="{{ route('equipment.show', $rental->equipment) }}"
                               class="text-lg font-bold text-gray-900 transition hover:text-indigo-600">
                                {{ $rental->equipment->name }}
                            </a>

                            @php
                                $activeBadgeClasses = [
                                    'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                                    'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                ];
                            @endphp

                            <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $activeBadgeClasses[$rental->status] ?? 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                {{ ucfirst($rental->status) }}
                            </span>
                        </div>

                        <dl class="grid gap-4 text-sm text-gray-600 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <dt class="font-bold text-gray-900">Rental Dates</dt>
                                <dd class="mt-1">
                                    {{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="font-bold text-gray-900">Purpose</dt>
                                <dd class="mt-1 break-words">{{ $rental->purpose }}</dd>
                            </div>

                            <div>
                                <dt class="font-bold text-gray-900">Club</dt>
                                <dd class="mt-1">
                                    {{ $rental->equipment->club->club_name ?? $rental->equipment->club->name ?? 'IIUM Club' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @if($rental->status === 'pending')
                        <form method="POST"
                              action="{{ route('rentals.cancel', $rental) }}"
                              onsubmit="return confirm('Cancel this rental request?');"
                              class="shrink-0">
                            @csrf
                            <button type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-bold text-rose-700 transition hover:bg-rose-100 sm:w-auto">
                                <i class="fas fa-xmark"></i>
                                Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-200 bg-white p-8 text-center">
                <h3 class="text-base font-bold text-gray-900">No active rentals</h3>
                <p class="mt-2 text-sm text-gray-500">Pending and approved rental requests will appear here.</p>
            </div>
        @endforelse
    </section>

    <section>
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">
                Rental History
                <span class="sr-only">Recent Booking History</span>
            </h2>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">
                Completed, Rejected &amp; Cancelled
            </span>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Equipment</th>
                            <th scope="col" class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Rental Dates</th>
                            <th scope="col" class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                            <th scope="col" class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Purpose</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rentalHistory as $rental)
                            @php
                                $historyBadgeClasses = [
                                    'completed' => 'border-blue-200 bg-blue-50 text-blue-700',
                                    'rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
                                    'cancelled' => 'border-gray-200 bg-gray-50 text-gray-600',
                                ];
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                <td class="px-5 py-4 align-top">
                                    <a href="{{ route('equipment.show', $rental->equipment) }}"
                                       class="font-bold text-indigo-600 transition hover:text-indigo-700">
                                        {{ $rental->equipment->name }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 align-top text-sm text-gray-600">
                                    {{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $historyBadgeClasses[$rental->status] ?? 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                        {{ ucfirst($rental->status) }}
                                    </span>
                                </td>
                                <td class="max-w-md px-5 py-4 align-top text-sm text-gray-600">
                                    <span class="block break-words">{{ $rental->purpose }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center">
                                    <h3 class="text-base font-bold text-gray-900">No rental history yet</h3>
                                    <p class="mt-2 text-sm text-gray-500">Completed, rejected, and cancelled rentals will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
