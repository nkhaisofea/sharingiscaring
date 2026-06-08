@props([
    'blockedDates' => [],
    'blockedRentals' => collect(),
    'months' => 2,
])

@php
    $blocked = collect($blockedDates);
    $startMonth = now()->startOfMonth();
    $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
            <i class="fas fa-calendar-alt text-indigo-500"></i>
            Availability Calendar
        </h2>
        <div class="flex items-center gap-4 text-xs font-semibold">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-100 border border-emerald-200"></span>
                Available
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-rose-100 border border-rose-200"></span>
                Booked
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        @for($m = 0; $m < $months; $m++)
            @php
                $month = $startMonth->copy()->addMonths($m);
                $daysInMonth = $month->daysInMonth;
                $firstDayOfWeek = $month->copy()->startOfMonth()->dayOfWeek;
            @endphp

            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3 text-center">{{ $month->format('F Y') }}</h3>

                <div class="grid grid-cols-7 gap-1 mb-1">
                    @foreach($dayLabels as $label)
                        <div class="text-center text-[10px] font-bold text-gray-400 uppercase py-1">{{ $label }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7 gap-1">
                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="aspect-square"></div>
                    @endfor

                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $date = $month->copy()->day($day);
                            $dateStr = $date->format('Y-m-d');
                            $isPast = $date->isPast() && !$date->isToday();
                            $isBlocked = $blocked->contains($dateStr);
                            $isToday = $date->isToday();

                            if ($isBlocked) {
                                $cellClass = 'bg-rose-50 text-rose-600 border-rose-100 font-bold';
                            } elseif ($isPast) {
                                $cellClass = 'bg-gray-50 text-gray-300 border-gray-100';
                            } else {
                                $cellClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                            }

                            if ($isToday) {
                                $cellClass .= ' ring-2 ring-indigo-400 ring-offset-1';
                            }
                        @endphp

                        <div class="aspect-square flex items-center justify-center text-xs rounded-lg border {{ $cellClass }}"
                             title="{{ $isBlocked ? 'Booked' : ($isPast ? 'Past' : 'Available') }} — {{ $date->format('d M Y') }}">
                            {{ $day }}
                        </div>
                    @endfor
                </div>
            </div>
        @endfor
    </div>

    @if($blockedRentals->count())
        <div class="border-t border-gray-100 pt-4">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Upcoming Bookings</h3>
            <div class="space-y-2">
                @foreach($blockedRentals as $rental)
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                            'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        ];
                    @endphp
                    <div class="flex items-center justify-between text-sm bg-gray-50 rounded-xl px-4 py-2.5 border border-gray-100">
                        <span class="font-semibold text-gray-800">
                            {{ $rental->start_date->format('d M') }} – {{ $rental->end_date->format('d M Y') }}
                        </span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border {{ $statusColors[$rental->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                            {{ ucfirst($rental->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500 border-t border-gray-100 pt-4">
            <i class="fas fa-check-circle text-emerald-500 mr-1"></i>
            No upcoming bookings — all dates are open for the next {{ $months }} months.
        </p>
    @endif
</div>
