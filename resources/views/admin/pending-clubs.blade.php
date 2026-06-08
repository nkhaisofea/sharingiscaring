@extends('layouts.app')

@section('title', 'Pending Club Approvals')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Super Admin</p>
        <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900">Pending Club Registrations</h1>
        <p class="mt-2 text-sm text-gray-500">Approve or reject club admin accounts before they can access the admin dashboard.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Club Name</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Email</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500">Submitted</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingClubs as $club)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $club->club_name }}</div>
                                <div class="text-xs text-gray-400">Pending approval</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $club->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $club->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('admin.pending-clubs.approve', $club) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-emerald-500 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-600">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pending-clubs.reject', $club) }}" onsubmit="return confirm('Reject this club registration?');">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-rose-500 px-3 py-2 text-xs font-bold text-white transition hover:bg-rose-600">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <h2 class="text-base font-bold text-gray-900">No pending club registrations</h2>
                                <p class="mt-2 text-sm text-gray-500">New club admin requests will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pendingClubs->hasPages())
        <div class="mt-6">
            {{ $pendingClubs->links() }}
        </div>
    @endif
</div>
@endsection
