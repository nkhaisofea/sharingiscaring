@extends('layouts.app')

@section('title', 'Manage Clubs')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Super Admin</p>
            <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900">Manage Clubs</h1>
            <p class="mt-2 text-sm text-gray-500">Review registered clubs, approval status, equipment volume, and active rental activity.</p>
        </div>
        <a href="{{ route('admin.pending-clubs') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-3 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100">
            <i class="fas fa-user-check"></i>
            Pending Approvals
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Club</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Registered</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Equipment</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Active Rentals</th>
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($clubs as $club)
                        @php
                            $status = $club->club_status ?? ($club->role === 'club_admin' ? 'approved' : 'pending');
                            $statusClasses = [
                                'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                                'rejected' => 'border-rose-200 bg-rose-50 text-rose-700',
                                'suspended' => 'border-gray-200 bg-gray-100 text-gray-700',
                            ];
                        @endphp
                        <tr class="align-top transition hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900">{{ $club->club_name }}</div>
                                <div class="mt-1 text-sm text-gray-500">{{ $club->email }}</div>
                                @if($club->rejection_reason)
                                    <div class="mt-2 max-w-xs rounded-lg bg-gray-50 px-3 py-2 text-xs font-medium text-gray-500">
                                        {{ $club->rejection_reason }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClasses[$status] ?? 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $club->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $club->equipment_count }}</td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $club->active_rentals_count }}</td>
                            <td class="px-5 py-4">
                                <div class="flex min-w-52 flex-col gap-2">
                                    @if($status === 'pending' || $status === 'rejected')
                                        <form method="POST" action="{{ route('admin.clubs.approve', $club) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-lg bg-emerald-500 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-600">Approve</button>
                                        </form>
                                    @endif

                                    @if($status === 'pending')
                                        <form method="POST" action="{{ route('admin.clubs.reject', $club) }}" class="space-y-2">
                                            @csrf
                                            <input type="text" name="rejection_reason" required placeholder="Reason for rejection" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <button type="submit" class="w-full rounded-lg bg-rose-500 px-3 py-2 text-xs font-bold text-white transition hover:bg-rose-600">Reject</button>
                                        </form>
                                    @endif

                                    @if($status === 'approved')
                                        <form method="POST" action="{{ route('admin.clubs.suspend', $club) }}" class="space-y-2">
                                            @csrf
                                            <input type="text" name="rejection_reason" placeholder="Suspension reason" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <button type="submit" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 transition hover:border-amber-400 hover:text-amber-700">Suspend</button>
                                        </form>
                                    @endif

                                    @if($status === 'suspended')
                                        <form method="POST" action="{{ route('admin.clubs.activate', $club) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700">Activate</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <h2 class="text-base font-bold text-gray-900">No clubs found</h2>
                                <p class="mt-2 text-sm text-gray-500">Club registrations will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($clubs->hasPages())
        <div class="mt-6">
            {{ $clubs->links() }}
        </div>
    @endif
</div>
@endsection
