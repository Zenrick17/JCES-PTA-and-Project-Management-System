@php
    $flashMessages = [
        [
            'key' => 'success',
            'title' => 'Success',
            'classes' => 'bg-green-50 border-green-200 text-green-800',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'key' => 'error',
            'title' => 'Error',
            'classes' => 'bg-red-50 border-red-200 text-red-800',
            'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'key' => 'warning',
            'title' => 'Warning',
            'classes' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
            'icon' => 'M12 9v2m0 4h.01M10.29 3.86l-6.91 12A1 1 0 004.25 17h13.5a1 1 0 00.87-1.5l-6.91-12a1 1 0 00-1.74 0z',
        ],
        [
            'key' => 'info',
            'title' => 'Info',
            'classes' => 'bg-blue-50 border-blue-200 text-blue-800',
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];
@endphp

<div class="fixed top-5 right-5 z-50 space-y-3">
    @foreach ($flashMessages as $flash)
        @if (session($flash['key']))
            <div x-data="{ show: true }"
                 x-init="setTimeout(() => show = false, 4000)"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="max-w-sm w-full border rounded-lg shadow-lg px-4 py-3 {{ $flash['classes'] }}">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $flash['icon'] }}" />
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold">{{ $flash['title'] }}</p>
                        <p class="text-sm">{{ session($flash['key']) }}</p>
                    </div>
                    <button type="button" @click="show = false" class="text-current/60 hover:text-current">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 6000)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="max-w-sm w-full border rounded-lg shadow-lg px-4 py-3 bg-red-50 border-red-200 text-red-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="font-semibold">Please fix the errors below.</p>
                    <ul class="mt-1 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" @click="show = false" class="text-current/60 hover:text-current">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif
</div>
