@extends('layouts.app')

@section('title', 'Browse Equipment')

@section('content')
<!-- Hero Section -->
<div class="gradient-bg text-white py-20">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-4 animate-fadeIn">Share Equipment, Build Community</h1>
        <p class="text-xl text-purple-100 mb-8">Rent equipment from IIUM clubs at affordable prices</p>
        <div class="max-w-2xl mx-auto">
            <form action="/equipment" method="GET" class="flex">
                <input type="text" name="search" placeholder="Search equipment..." 
                       class="flex-1 px-6 py-3 rounded-l-lg text-gray-900 focus:outline-none"
                       value="{{ request('search') }}">
                <button type="submit" class="bg-white text-indigo-600 px-6 py-3 rounded-r-lg font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Equipment Grid -->
<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($equipment as $item)
            <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 gradient-bg flex items-center justify-center">
                        <i class="fas fa-microphone-alt text-white text-6xl"></i>
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-1">{{ $item->name }}</h3>
                    <p class="text-gray-600 text-sm mb-2">{{ Str::limit($item->description, 80) }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-indigo-600 font-bold text-xl">RM {{ number_format($item->price_per_day, 2) }}</span>
                        <span class="text-gray-500 text-xs">/day</span>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <a href="/equipment/{{ $item->id }}" class="flex-1 bg-indigo-600 text-white text-center py-2 rounded-lg hover:bg-indigo-700 transition text-sm">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600">No equipment found</h3>
                <p class="text-gray-500">Check back later for available equipment</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-8">
        {{ $equipment->links() }}
    </div>
</div>
@endsection