@extends('layouts.app')

@section('title', 'Browse Equipment')

@section('content')
<div class="gradient-bg text-white py-16">
    <div class="max-w-7xl mx-auto px-4 text-center sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold mb-4 animate-fadeIn sm:text-5xl">Share Equipment, Build Community</h1>
        <p class="text-lg text-purple-100">Rent equipment from IIUM clubs at affordable prices</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8" x-data="{ filtersOpen: false }">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Browse Equipment</h2>
            <p class="mt-1 text-sm font-medium text-gray-500">{{ $resultCount }} result{{ $resultCount === 1 ? '' : 's' }} found</p>
        </div>
        <button type="button"
                @click="filtersOpen = !filtersOpen"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-600 lg:hidden">
            <i class="fas fa-sliders"></i>
            Filters
        </button>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px,1fr]">
        <aside class="lg:block" :class="filtersOpen ? 'block' : 'hidden'">
            <form action="{{ route('equipment.index') }}" method="GET" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <label for="search" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Search</label>
                    <div class="relative">
                        <i class="fas fa-magnifying-glass absolute left-3 top-3.5 text-gray-400"></i>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Camera, tent, speaker..."
                               class="w-full rounded-xl border border-gray-200 py-3 pl-10 pr-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="mb-5">
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Category</label>
                    <select id="category" name="category" class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Price Range</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-xs font-bold text-gray-400">RM</span>
                            <input type="number"
                                   name="min_price"
                                   value="{{ request('min_price') }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="Min"
                                   class="w-full rounded-xl border border-gray-200 py-3 pl-9 pr-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-xs font-bold text-gray-400">RM</span>
                            <input type="number"
                                   name="max_price"
                                   value="{{ request('max_price') }}"
                                   min="0"
                                   step="0.01"
                                   placeholder="Max"
                                   class="w-full rounded-xl border border-gray-200 py-3 pl-9 pr-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="availability" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Availability</label>
                    <select id="availability" name="availability" class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="available" {{ request('availability', 'available') === 'available' ? 'selected' : '' }}>Available only</option>
                        <option value="all" {{ request('availability') === 'all' ? 'selected' : '' }}>All equipment</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="sort" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Sort By</label>
                    <select id="sort" name="sort" class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest first</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                        Apply
                    </button>
                    <a href="{{ route('equipment.index') }}" class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                        Clear
                    </a>
                </div>
            </form>
        </aside>

        <section>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($equipment as $item)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover border border-gray-100">
                        <div class="relative">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 gradient-bg flex items-center justify-center">
                                    <i class="fas {{ $item->category->icon ?? 'fa-box-open' }} text-white text-6xl"></i>
                                </div>
                            @endif
                            <span class="absolute left-3 top-3 rounded-full border px-2.5 py-1 text-xs font-bold {{ $item->availability_status === 'available' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-white text-gray-600' }}">
                                {{ ucfirst($item->availability_status) }}
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="mb-2 flex items-start justify-between gap-3">
                                <h3 class="font-bold text-lg text-gray-900">{{ $item->name }}</h3>
                                <span class="shrink-0 rounded-lg bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-700">{{ $item->category->name }}</span>
                            </div>
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($item->description, 80) }}</p>
                            <div class="flex justify-between items-end">
                                <div>
                                    <span class="text-indigo-600 font-black text-xl">RM {{ number_format($item->price_per_day, 2) }}</span>
                                    <span class="text-gray-500 text-xs">/day</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-400">{{ $item->rentals_count }} rental{{ $item->rentals_count === 1 ? '' : 's' }}</span>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('equipment.show', $item) }}" class="block w-full rounded-lg bg-indigo-600 py-2.5 text-center text-sm font-bold text-white transition hover:bg-indigo-700">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-white py-16 text-center">
                        <i class="fas fa-search text-gray-300 text-5xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-700">No equipment found</h3>
                        <p class="text-gray-500 mt-2">Try clearing filters or broadening your search.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-8">
                {{ $equipment->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
