@props([
    'show' => 'showModal',
    'title' => '',
    'icon' => null,
    'iconClass' => 'text-indigo-600',
    'maxWidth' => 'max-w-md',
])

<div x-show="{{ $show }}"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="{{ $show }} = false"></div>

    <div {{ $attributes->merge(['class' => "bg-white rounded-2xl shadow-2xl w-full {$maxWidth} overflow-hidden relative z-10 border border-gray-100"]) }}
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        @if($title)
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-2">
                    @if($icon)
                        <i class="fas {{ $icon }} {{ $iconClass }}"></i>
                    @endif
                    <h3 class="text-lg font-bold text-gray-900">{{ $title }}</h3>
                </div>
                <button type="button" @click="{{ $show }} = false" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
