<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'JCES-PTA') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-52 bg-gradient-to-b from-green-500 to-green-600 flex flex-col">
            <!-- Logo and School Name -->
            <div class="p-6 bg-white border-r border-gray-200">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logos/jces-logo.png') }}"
                         alt="JCES Logo"
                         class="w-12 h-12 object-contain">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">J. Cruz Sr.</div>
                        <div class="text-xs text-gray-600">Elementary School</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-3 py-6 space-y-2">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 text-white {{ request()->routeIs('dashboard') ? 'bg-green-700' : 'hover:bg-green-700' }} rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    <span>Home</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 text-white hover:bg-green-700 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    <span>Announcements</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 text-white hover:bg-green-700 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                        <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                    </svg>
                    <span>Projects</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 text-white hover:bg-green-700 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Payments</span>
                </a>
            </nav>

            <!-- User Profile and Sign Out -->
            <div class="p-4 border-t border-green-700">
                <a href="{{ route('sign-out') }}" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-green-700 rounded-lg font-medium transition-colors w-full mb-3 block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sign Out</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-green-700 rounded-lg font-medium transition-colors w-full">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-semibold text-sm">
                            {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->username ?? 'U', 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1)) }}
                        </span>
                    </div>
                    <div class="text-white">
                        <div class="text-sm font-semibold">{{ Auth::user()->name ?? Auth::user()->username ?? 'User' }}</div>
                        <div class="text-xs opacity-90">Parent</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Header -->
            @if (isset($header))
            <header class="bg-gradient-to-r from-green-50 to-green-100 border-b border-gray-200 px-8 py-4">
                <div class="flex items-center justify-between">
                    {{ $header }}

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 focus:outline-none">
                            <span>{{ Auth::user()->name ?? 'User' }}</span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                             style="display: none;">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            @endif

            <!-- Page Content -->
            <main class="p-8">
                @include('components.flash-messages')
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Alpine.js for dropdown functionality -->
    @include('components.inactivity-timeout')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
