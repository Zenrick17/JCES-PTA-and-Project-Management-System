@extends('layouts.te-sidebar')

@section('content')
    <!-- Teacher Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- My Students Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase">My Students</span>
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['myStudents'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Enrolled students</div>
        </div>

        <!-- Active Parents Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase">Active Parents</span>
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['activeParents'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Registered accounts</div>
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

        <!-- Proposed Projects Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase">Proposed Projects</span>
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['proposedProjects'] ?? 0 }}</div>
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
                        @else border-gray-300
                        @endif">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</h3>
                        <p class="text-xs text-gray-600 mt-2">{{ Str::limit($announcement->content, 80) }}</p>
                        <div class="text-xs text-gray-400 mt-2">{{ $announcement->time_ago }}</div>
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
                            <span class="text-xs font-medium text-gray-500">{{ $schedule->formatted_date }}</span>
                            <h3 class="text-sm font-semibold text-gray-900">{{ $schedule->title }}</h3>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">{{ Str::limit($schedule->description, 80) }}</p>
                        @if($schedule->time_range)
                            <div class="text-xs text-gray-500 mt-2">{{ $schedule->time_range }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-sm text-gray-500 text-center py-4">No upcoming schedules</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Projects Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-base font-bold text-gray-900 mb-4">Active Projects</h3>
        <div class="space-y-3">
            @forelse($activeProjects ?? [] as $project)
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4
                    @if($project->project_status === 'active') border-green-500
                    @elseif($project->project_status === 'in_progress') border-blue-500
                    @else border-gray-500
                    @endif">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">{{ $project->project_name }}</h3>
                        <span class="text-xs text-gray-400">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'TBA' }}</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">{{ Str::limit($project->description, 100) }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="px-2 py-1 text-xs rounded-full font-medium
                            @if($project->project_status === 'active') bg-green-100 text-green-700
                            @elseif($project->project_status === 'in_progress') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $project->project_status)) }}
                        </span>
                        @if($project->target_budget)
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                Target: ₱{{ number_format($project->target_budget, 2) }}
                            </span>
                        @endif
                        @if($project->current_amount)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                Raised: ₱{{ number_format($project->current_amount, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-sm text-gray-500 text-center py-4">No active projects at the moment</div>
            @endforelse
        </div>
    </div>
@endsection
