@extends('layouts.parent-sidebar')

@section('title', 'Dashboard')

@section('content')
    @include('components.dashboard-notice')

    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- My Children Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase">My Children</span>
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['childrenCount'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">
                @if(($stats['childrenCount'] ?? 0) === 1)
                    {{ $children->first()->grade_level ?? 'N/A' }} - {{ $children->first()->section ?? 'N/A' }}
                @elseif(($stats['childrenCount'] ?? 0) > 1)
                    {{ $children->pluck('grade_level')->unique()->implode(', ') }}
                @else
                    No children enrolled
                @endif
            </div>
        </div>

        <!-- Outstanding Balance Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase">Outstanding Balance</span>
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">₱2,450</div>
            <div class="text-sm text-gray-500 mt-1">Due March 20, 2024</div>
        </div>

        <!-- Upcoming Events Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase">Upcoming Events</span>
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['upcomingEvents'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">This month</div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Announcements -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4">Recent Announcements</h3>
            <div class="space-y-3">
                @forelse($recentAnnouncements ?? [] as $announcement)
                    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4
                        @if($announcement->category === 'important') border-red-500
                        @elseif($announcement->category === 'notice') border-orange-500
                        @elseif($announcement->category === 'update') border-blue-500
                        @elseif($announcement->category === 'event') border-green-500
                        @else border-gray-500
                        @endif">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</h3>
                            <span class="text-xs text-gray-400">{{ $announcement->time_ago }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">{{ Str::limit($announcement->content, 80) }}</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($announcement->category === 'important') bg-red-100 text-red-700
                                @elseif($announcement->category === 'notice') bg-orange-100 text-orange-700
                                @elseif($announcement->category === 'update') bg-blue-100 text-blue-700
                                @elseif($announcement->category === 'event') bg-green-100 text-green-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($announcement->category) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 text-center py-4">No recent announcements</div>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Schedule -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4">Upcoming Schedule</h3>
            <div class="space-y-3">
                @forelse($upcomingSchedules ?? [] as $schedule)
                    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4
                        @if($schedule->priority === 'high') border-red-500
                        @elseif($schedule->priority === 'medium') border-purple-500
                        @else border-blue-500
                        @endif">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $schedule->title }}</h3>
                            <span class="text-xs text-gray-400">{{ $schedule->formatted_date }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">{{ Str::limit($schedule->description, 80) }}</p>
                        <div class="flex items-center gap-2 mt-3">
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($schedule->priority === 'high') bg-red-100 text-red-700
                                @elseif($schedule->priority === 'medium') bg-purple-100 text-purple-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                {{ ucfirst($schedule->priority) }} Priority
                            </span>
                            @if($schedule->time_range)
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                    {{ $schedule->time_range }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 text-center py-4">No upcoming schedules</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- About Us Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-bold text-gray-900 mb-4">About US</h3>
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0">
                <img src="{{ asset('images/logos/jces-logo.png') }}"
                     alt="School Photo"
                     class="rounded-lg w-full md:w-52 h-28 object-cover">
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-700 leading-relaxed">
                    J. Cruz Sr. Elementary School, founded in 1975 on land donated by Juan Cruz Sr. in Panacan,
                    Relocation, Davao City, has grown from modest beginnings into a key educational institution. For
                    nearly five decades, it has served both local and neighboring communities, fostering strong ties
                    through active PTA involvement and continuous school improvement projects.
                </p>
            </div>
        </div>
    </div>
@endsection
