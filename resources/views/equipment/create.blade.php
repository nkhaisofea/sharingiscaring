@extends('layouts.app')

@section('title', 'Add New Equipment')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900 font-medium">Add Equipment</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
            <div class="w-10 h-10 gradient-bg text-white rounded-xl flex items-center justify-center text-lg shadow-md shadow-indigo-100">
                <i class="fas fa-circle-plus"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New Equipment</h1>
                <p class="text-sm text-gray-500 font-medium">List a new equipment available for IIUM club members to rent.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('equipment.store') }}" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Equipment Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       placeholder="e.g. Sony Alpha 7 III DSLR Camera"
                       required
                       class="w-full px-4 py-3 border @error('name') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition">
                @error('name')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                    <select name="category_id" 
                            id="category_id" 
                            required
                            class="w-full px-4 py-3 border @error('category_id') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition bg-white">
                        <option value="" disabled selected>Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Condition -->
                <div>
                    <label for="condition" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Condition</label>
                    <select name="condition" 
                            id="condition" 
                            required
                            class="w-full px-4 py-3 border @error('condition') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition bg-white">
                        <option value="" disabled selected>Select Condition</option>
                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                    @error('condition')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                <textarea name="description" 
                          id="description" 
                          rows="4" 
                          placeholder="Describe the equipment details, package items (e.g. battery, charger, bag included), and any specific guidelines for usage..."
                          required
                          class="w-full px-4 py-3 border @error('description') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Price Per Day -->
                <div>
                    <label for="price_per_day" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Price Per Day (RM)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-sm font-bold text-gray-400">RM</span>
                        <input type="number" 
                               name="price_per_day" 
                               id="price_per_day" 
                               value="{{ old('price_per_day') }}"
                               placeholder="0.00"
                               step="0.01"
                               min="0"
                               required
                               class="w-full pl-12 pr-4 py-3 border @error('price_per_day') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition">
                    </div>
                    @error('price_per_day')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pickup Location -->
                <div>
                    <label for="pickup_location" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pickup Location</label>
                    <input type="text" 
                           name="pickup_location" 
                           id="pickup_location" 
                           value="{{ old('pickup_location') }}"
                           placeholder="e.g. Kulliyyah of ICT, Office Level 2"
                           required
                           class="w-full px-4 py-3 border @error('pickup_location') border-rose-500 focus:ring-rose-500 @else border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 @enderror rounded-xl focus:outline-none focus:ring-2 text-sm transition">
                    @error('pickup_location')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Equipment Image</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-xl hover:border-indigo-500 transition duration-200" x-data="{ fileName: '' }">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-image text-gray-400 text-3xl mb-3"></i>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-bold text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <span>Upload a file</span>
                                <input id="image" 
                                       name="image" 
                                       type="file" 
                                       accept="image/*"
                                       class="sr-only"
                                       @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-400">PNG, JPG, JPEG up to 2MB</p>
                        <p class="text-xs text-indigo-600 font-bold mt-2" x-show="fileName" x-text="'Selected file: ' + fileName"></p>
                    </div>
                </div>
                @error('image')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Controls -->
            <div class="flex gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 py-3.5 border border-gray-200 rounded-xl text-center text-gray-700 font-semibold hover:bg-gray-50 transition duration-200 text-sm">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3.5 rounded-xl font-bold hover:shadow-lg hover:shadow-indigo-100 active:scale-[0.99] transition duration-200 text-sm">
                    Save Equipment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
