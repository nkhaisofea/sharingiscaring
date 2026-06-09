@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="gradient-bg text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[1.1fr,0.9fr] lg:px-8 lg:py-24">
        <div class="flex flex-col justify-center">
            <p class="mb-3 text-sm font-bold uppercase tracking-wider text-purple-100">IIUM Club Equipment Rental</p>
            <h1 class="text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">Share Equipment, Build Community at IIUM</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-purple-50">Borrow event, media, outdoor, and club equipment from trusted IIUM clubs without chasing scattered forms or group chats.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('equipment.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-extrabold text-indigo-700 shadow-lg transition hover:bg-indigo-50">
                    <i class="fas fa-magnifying-glass"></i>
                    Browse Equipment
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 px-6 py-3 text-sm font-extrabold text-white transition hover:bg-white/10">
                    <i class="fas fa-user-plus"></i>
                    Register
                </a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 self-end rounded-2xl bg-white/10 p-4 backdrop-blur">
            <div class="rounded-xl bg-white p-4 text-center text-gray-900">
                <div class="text-3xl font-black text-indigo-600">{{ number_format($stats['clubs']) }}</div>
                <div class="mt-1 text-xs font-bold uppercase tracking-wider text-gray-500">Clubs</div>
            </div>
            <div class="rounded-xl bg-white p-4 text-center text-gray-900">
                <div class="text-3xl font-black text-indigo-600">{{ number_format($stats['equipment']) }}</div>
                <div class="mt-1 text-xs font-bold uppercase tracking-wider text-gray-500">Equipment</div>
            </div>
            <div class="rounded-xl bg-white p-4 text-center text-gray-900">
                <div class="text-3xl font-black text-indigo-600">{{ number_format($stats['rentals']) }}</div>
                <div class="mt-1 text-xs font-bold uppercase tracking-wider text-gray-500">Rentals</div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <p class="text-sm font-bold uppercase tracking-wider text-indigo-600">How It Works</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-gray-900">Register, browse, and rent in three steps</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @foreach([
                ['title' => 'Register', 'icon' => 'fa-user-plus', 'copy' => 'Create a student member account or register your club for admin approval.'],
                ['title' => 'Browse', 'icon' => 'fa-magnifying-glass', 'copy' => 'Search equipment by category, price, availability, and popularity.'],
                ['title' => 'Rent', 'icon' => 'fa-calendar-check', 'copy' => 'Choose your rental dates and confirm an available item instantly.'],
            ] as $step)
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-6 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white">
                        <i class="fas {{ $step['icon'] }}"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-gray-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-500">{{ $step['copy'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wider text-indigo-600">Featured Equipment</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-gray-900">Available around the community</h2>
            </div>
            <a href="{{ route('equipment.index') }}" class="inline-flex items-center gap-2 text-sm font-extrabold text-indigo-600 hover:text-indigo-700">
                View all equipment
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($featuredEquipment as $item)
                <a href="{{ route('equipment.show', $item) }}" class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="h-40 w-full object-cover">
                    @else
                        <div class="gradient-bg flex h-40 w-full items-center justify-center text-white">
                            <i class="fas {{ $item->category->icon ?? 'fa-box-open' }} text-4xl"></i>
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="mb-2 text-xs font-bold uppercase tracking-wider text-indigo-600">{{ $item->category->name }}</div>
                        <h3 class="font-extrabold text-gray-900">{{ $item->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $item->club->club_name ?? $item->club->name }}</p>
                        <div class="mt-3 text-lg font-black text-indigo-600">RM {{ number_format($item->price_per_day, 2) }} <span class="text-xs font-semibold text-gray-400">/ day</span></div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center">
                    <h3 class="text-lg font-bold text-gray-900">No equipment yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Seed sample equipment or add listings from a club admin account.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
