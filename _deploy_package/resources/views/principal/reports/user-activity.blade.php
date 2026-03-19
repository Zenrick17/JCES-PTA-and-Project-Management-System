@extends('layouts.pr-sidebar')

@section('title', 'User Activity Statistics')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Activity Statistics</h1>
                <p class="text-gray-600 mt-1">Usage patterns and activity trends analysis</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('principal.reports') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    Back to Reports
                </a>
                <form method="GET" class="flex items-center gap-2">
                    <select name="days" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" onchange="this.form.submit()">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 Days</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Activity Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Activities</p>
                    <p class="text-3xl font-bold text-gray-900">{{ array_sum($actionStats) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Last {{ $days }} days</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Daily Average</p>
                    <p class="text-3xl font-bold text-green-600">{{ round(array_sum($actionStats) / $days) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Activities per day</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-3xl font-bold text-purple-600">{{ count($userStats) }}</p>
                    <p class="text-xs text-gray-500 mt-1">With activity</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Peak Hour</p>
                    @php
                        $peakHour = $loginPatterns->sortByDesc('count')->first();
                    @endphp
                    <p class="text-3xl font-bold text-orange-600">{{ $peakHour ? $peakHour->hour . ':00' : 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Most login activity</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Activity Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Activity Trend</h3>
        <div class="h-64 flex items-end justify-between gap-2">
            @php
                $maxCount = $dailyActivity->max('count') ?: 1;
            @endphp
            @foreach($dailyActivity as $day)
            <div class="flex flex-col items-center group">
                <div class="bg-blue-500 rounded-t-sm transition-all duration-200 group-hover:bg-blue-600 relative"
                     style="height: {{ ($day->count / $maxCount) * 200 }}px; width: 20px; min-height: 4px;">
                    <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                        {{ $day->count }} activities
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-2 transform -rotate-45 origin-top-left">
                    {{ \Carbon\Carbon::parse($day->date)->format('M j') }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top Actions and Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Common Actions</h3>
            <div class="space-y-4">
                @foreach(array_slice($actionStats, 0, 8, true) as $action => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full
                            @if(str_contains($action, 'login')) bg-blue-500
                            @elseif(str_contains($action, 'create')) bg-green-500
                            @elseif(str_contains($action, 'update')) bg-yellow-500
                            @elseif(str_contains($action, 'delete')) bg-red-500
                            @else bg-gray-500
                            @endif"></div>
                        <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $action) }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300
                                @if(str_contains($action, 'login')) bg-blue-500
                                @elseif(str_contains($action, 'create')) bg-green-500
                                @elseif(str_contains($action, 'update')) bg-yellow-500
                                @elseif(str_contains($action, 'delete')) bg-red-500
                                @else bg-gray-500
                                @endif"
                                style="width: {{ count($actionStats) > 0 ? max(10, ($count / max(array_values($actionStats))) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Active Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Active Users</h3>
            <div class="space-y-4">
                @foreach(array_slice($userStats, 0, 8) as $index => $user)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 font-medium text-xs">{{ $index + 1 }}</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $user['user'] }}</div>
                            <div class="text-xs text-gray-500">{{ $user['count'] }} activities</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                style="width: {{ count($userStats) > 0 ? max(10, ($user['count'] / max(array_column($userStats, 'count'))) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-6 text-right">{{ $user['count'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Login Pattern Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Login Patterns (Hourly Distribution)</h3>
        <div class="grid grid-cols-12 gap-2">
            @php
                $maxLogins = $loginPatterns->max('count') ?: 1;
                $hourlyData = [];
                for($i = 0; $i < 24; $i++) {
                    $hourlyData[$i] = $loginPatterns->where('hour', $i)->first()?->count ?? 0;
                }
            @endphp
            @foreach($hourlyData as $hour => $count)
            <div class="text-center">
                <div class="mb-2">
                    <div class="bg-blue-500 rounded-t-sm mx-auto transition-all duration-200 hover:bg-blue-600"
                         style="height: {{ ($count / $maxLogins) * 80 }}px; width: 16px; min-height: 2px;">
                    </div>
                </div>
                <div class="text-xs text-gray-500">{{ sprintf('%02d', $hour) }}</div>
                <div class="text-xs text-gray-400">{{ $count }}</div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Peak Hours:</strong> 
            @php
                $topHours = collect($hourlyData)->sortDesc()->take(3);
            @endphp
            @foreach($topHours as $hour => $count)
                {{ sprintf('%02d:00', $hour) }}@if(!$loop->last), @endif
            @endforeach
            </p>
        </div>
    </div>

    <!-- Activity Insights -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Insights</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h4 class="font-medium text-gray-900">Usage Patterns</h4>
                <p class="text-sm text-gray-600 mt-1">
                    @if($peakHour && $peakHour->hour >= 8 && $peakHour->hour <= 17)
                        Peak activity during business hours indicates normal usage patterns.
                    @elseif($peakHour && ($peakHour->hour < 6 || $peakHour->hour > 22))
                        Peak activity during off-hours may indicate unusual access patterns.
                    @else
                        Activity patterns appear normal for the selected period.
                    @endif
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
                <h4 class="font-medium text-gray-900">User Engagement</h4>
                <p class="text-sm text-gray-600 mt-1">
                    {{ count($userStats) }} users have been active in the last {{ $days }} days, 
                    with an average of {{ count($userStats) > 0 ? round(array_sum(array_column($userStats, 'count')) / count($userStats)) : 0 }} activities per user.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <h4 class="font-medium text-gray-900">System Performance</h4>
                <p class="text-sm text-gray-600 mt-1">
                    @php $avgDaily = round(array_sum($actionStats) / $days) @endphp
                    System is handling {{ $avgDaily }} activities per day on average.
                    @if($avgDaily > 100)
                        High activity levels indicate good system utilization.
                    @else
                        Current activity levels are within normal ranges.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
