@extends('layouts.app')

@section('title', $equipment->name)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8"
     id="equipment-show"
     x-data="{
         showModal: false,
         startDate: '{{ old('start_date') }}',
         endDate: '{{ old('end_date') }}',
         pricePerDay: {{ $equipment->price_per_day }},
         blockedDates: @json($blockedDates),
         startPicker: null,
         endPicker: null,
         get days() {
             if (!this.startDate || !this.endDate) return 0;
             const start = new Date(this.startDate + 'T00:00:00');
             const end = new Date(this.endDate + 'T00:00:00');
             if (end < start) return 0;
             return Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
         },
         get totalPrice() {
             return (this.days * this.pricePerDay).toFixed(2);
         },
         openRentalModal() {
             this.showModal = true;
             this.$nextTick(() => this.initDatePickers());
         },
         initDatePickers() {
             if (typeof flatpickr === 'undefined') return;
             if (this.startPicker) return;

             const self = this;
             const baseConfig = {
                 minDate: 'today',
                 dateFormat: 'Y-m-d',
                 disable: this.blockedDates,
             };

             this.startPicker = flatpickr(this.$refs.startInput, {
                 ...baseConfig,
                 defaultDate: this.startDate || null,
                 onChange(selectedDates, dateStr) {
                     self.startDate = dateStr;
                     if (self.endPicker) {
                         self.endPicker.set('minDate', dateStr || 'today');
                         if (self.endDate && self.endDate < dateStr) {
                             self.endPicker.clear();
                             self.endDate = '';
                         }
                     }
                 },
             });

             this.endPicker = flatpickr(this.$refs.endInput, {
                 ...baseConfig,
                 minDate: this.startDate || 'today',
                 defaultDate: this.endDate || null,
                 onChange(selectedDates, dateStr) {
                     self.endDate = dateStr;
                 },
             });
         }
     }"
     @keydown.escape.window="showModal = false">

    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('equipment.index') }}" class="hover:text-indigo-600 transition duration-200">Equipment</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-900 font-medium">{{ $equipment->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        {{-- Image Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex items-center justify-center min-h-[300px] lg:min-h-[400px]">
            @if($equipment->image)
                <img src="{{ asset('storage/' . $equipment->image) }}"
                     alt="{{ $equipment->name }}"
                     class="w-full h-full object-cover max-h-[500px]">
            @else
                <div class="w-full h-full min-h-[350px] lg:min-h-[450px] gradient-bg flex flex-col items-center justify-center text-white p-8">
                    <div class="w-24 h-24 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-4 shadow-inner">
                        <i class="fas {{ $equipment->category->icon ?? 'fa-box-open' }} text-white text-5xl"></i>
                    </div>
                    <span class="text-sm font-semibold tracking-wider uppercase opacity-85">{{ $equipment->category->name }}</span>
                </div>
            @endif
        </div>

        {{-- Details Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex flex-col justify-between">
            <div>
                {{-- Badges --}}
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $equipment->category->name }}
                    </span>

                    @php
                        $conditionColors = [
                            'new' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'excellent' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'good' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                            'fair' => 'bg-amber-50 text-amber-700 border-amber-100',
                            'poor' => 'bg-rose-50 text-rose-700 border-rose-100',
                        ];
                        $statusColors = [
                            'available' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            'rented' => 'bg-rose-50 text-rose-700 border-rose-100',
                            'maintenance' => 'bg-amber-50 text-amber-700 border-amber-100',
                        ];
                    @endphp

                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $conditionColors[$equipment->condition] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                        {{ ucfirst($equipment->condition) }} Condition
                    </span>

                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$equipment->availability_status] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                        {{ ucfirst($equipment->availability_status) }}
                    </span>
                </div>

                {{-- Name & Description --}}
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-4">{{ $equipment->name }}</h1>
                <p class="text-gray-600 mb-6 leading-relaxed whitespace-pre-line">{{ $equipment->description }}</p>

                {{-- Price Tag --}}
                <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between mb-6 border border-gray-100">
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider block font-semibold">Rental Price</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold text-indigo-600">
                                RM {{ number_format($equipment->price_per_day, 2) }}
                            </span>
                            <span class="text-sm text-gray-500 font-medium">/ day</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500 uppercase tracking-wider block font-semibold">Location</span>
                        <span class="text-sm font-semibold text-gray-800 flex items-center gap-1.5 justify-end">
                            <i class="fas fa-map-marker-alt text-indigo-500"></i>
                            {{ $equipment->pickup_location }}
                        </span>
                    </div>
                </div>

                {{-- Club Info Card --}}
                <div class="border border-gray-100 rounded-xl p-4 mb-8 bg-white shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="gradient-bg w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-indigo-100">
                            {{ $equipment->club->initials }}
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase tracking-wider block font-semibold">Managed By</span>
                            <p class="font-bold text-gray-900">
                                {{ $equipment->club->club_name ?? $equipment->club->name }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-700 rounded-lg">IIUM Club</span>
                </div>
            </div>

            {{-- Rental Request Controls --}}
            <div>
                @if($equipment->isAvailable())
                    @auth
                        @if(auth()->id() === $equipment->club_id && !auth()->user()->isSuperAdmin())
                            <div class="space-y-3">
                                <div class="w-full text-center text-gray-500 py-4 bg-gray-50 border border-gray-100 rounded-xl font-medium flex items-center justify-center gap-2">
                                    <i class="fas fa-user-shield text-gray-400"></i>
                                    This is your equipment listing
                                </div>
                                <a href="{{ route('equipment.edit', $equipment) }}"
                                   class="w-full border-2 border-indigo-200 text-indigo-700 py-3.5 rounded-xl font-bold hover:bg-indigo-50 transition duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-edit"></i>
                                    Edit Listing
                                </a>
                            </div>
                        @else
                            <button type="button"
                                    @click="openRentalModal()"
                                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-indigo-100 active:scale-[0.99] transition duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-calendar-check text-lg"></i>
                                Rent This Equipment
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 rounded-xl font-bold hover:shadow-lg hover:shadow-indigo-100 transition duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i> Login to Rent
                        </a>
                    @endauth
                @else
                    <button disabled
                            class="w-full bg-gray-100 text-gray-400 border border-gray-200 py-4 rounded-xl font-bold cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-ban"></i>
                        Currently Unavailable ({{ ucfirst($equipment->availability_status) }})
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Availability Calendar --}}
    <div class="mb-12">
        <x-availability-calendar :blocked-dates="$blockedDates" :blocked-rentals="$blockedRentals" />
    </div>

    {{-- Related Equipment --}}
    @if($relatedEquipment->count())
        <div class="border-t border-gray-100 pt-12">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-cubes text-indigo-500"></i>
                Related Equipment
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedEquipment as $item)
                    <a href="{{ route('equipment.show', $item) }}"
                       class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden card-hover block group">
                        <div class="h-44 w-full relative overflow-hidden bg-gray-100">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     alt="{{ $item->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full gradient-bg flex items-center justify-center group-hover:scale-105 transition duration-300">
                                    <i class="fas {{ $item->category->icon ?? 'fa-box-open' }} text-white text-4xl opacity-90"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <span class="text-[10px] uppercase tracking-wider font-extrabold text-indigo-500 mb-1 block">{{ $item->category->name }}</span>
                            <h3 class="font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition line-clamp-1">{{ $item->name }}</h3>
                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-50">
                                <span class="text-indigo-600 font-extrabold text-sm">
                                    RM {{ number_format($item->price_per_day, 2) }} <span class="text-xs text-gray-400 font-normal">/ day</span>
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md">{{ ucfirst($item->condition) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Rental Modal --}}
    @auth
        @if($equipment->isAvailable() && (auth()->id() !== $equipment->club_id || auth()->user()->isSuperAdmin()))
            <x-modal show="showModal" title="Request Rental" icon="fa-calendar-alt">
                <form method="POST" action="{{ route('rentals.store', $equipment) }}" class="p-6">
                    @csrf

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-rose-50 border border-rose-100 rounded-xl text-sm text-rose-700">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Date</label>
                            <input type="text"
                                   x-ref="startInput"
                                   name="start_date"
                                   placeholder="Select start date"
                                   required
                                   readonly
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white cursor-pointer">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">End Date</label>
                            <input type="text"
                                   x-ref="endInput"
                                   name="end_date"
                                   placeholder="Select end date"
                                   required
                                   readonly
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white cursor-pointer">
                        </div>
                    </div>

                    @if(count($blockedDates))
                        <p class="text-xs text-gray-500 mb-4 flex items-center gap-1.5">
                            <i class="fas fa-info-circle text-indigo-400"></i>
                            Booked dates are disabled in the picker.
                        </p>
                    @endif

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Purpose of Rental</label>
                        <textarea name="purpose"
                                  rows="3"
                                  required
                                  maxlength="500"
                                  placeholder="Explain the purpose of renting this equipment (e.g. event details, duration, etc.)"
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm placeholder-gray-400">{{ old('purpose') }}</textarea>
                    </div>

                    {{-- Order Summary --}}
                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 mb-6" x-show="days > 0" x-transition>
                        <div class="flex justify-between text-xs text-gray-600 mb-1.5">
                            <span class="font-medium">Rental Period</span>
                            <span class="font-bold text-gray-900" x-text="days + ' day(s)'"></span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 mb-3 pb-3 border-b border-indigo-100/50">
                            <span class="font-medium">Rate per Day</span>
                            <span class="font-bold text-gray-900" x-text="'RM ' + pricePerDay.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm font-bold text-indigo-900">Total Price</span>
                            <span class="text-xl font-black text-indigo-600" x-text="'RM ' + totalPrice"></span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button"
                                @click="showModal = false"
                                class="flex-1 py-3 border border-gray-200 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition active:scale-[0.98] text-sm">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="days <= 0"
                                class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-bold hover:shadow-lg hover:shadow-indigo-100 active:scale-[0.98] transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                            Submit Request
                        </button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endauth
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @if(old('start_date') && auth()->check() && (auth()->id() !== $equipment->club_id || auth()->user()->isSuperAdmin()))
        <script>
            document.addEventListener('alpine:initialized', () => {
                const root = document.getElementById('equipment-show');
                if (root && root._x_dataStack) {
                    root._x_dataStack[0].showModal = true;
                    root._x_dataStack[0].$nextTick(() => root._x_dataStack[0].initDatePickers());
                }
            });
        </script>
    @endif
@endpush
