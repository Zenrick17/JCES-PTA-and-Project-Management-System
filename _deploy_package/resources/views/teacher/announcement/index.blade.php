@extends('layouts.te-sidebar')

@section('title', 'Announcements')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">School Announcements</h1>
        <div class="relative">
            <button type="button" id="announcementFilterButton" class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                <span id="announcementFilterLabel">Filter</span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            <div id="announcementFilterMenu" class="absolute right-0 mt-2 w-44 bg-green-600 text-white rounded-lg shadow-lg hidden z-10">
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-green-700" data-filter="Today">Today</button>
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-green-700" data-filter="This Week">This Week</button>
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-green-700" data-filter="This Month">This Month</button>
            </div>
        </div>
    </div>

    <!-- Combined Announcements and Schedules -->
    <div class="space-y-5">
        <!-- Upcoming Schedules -->
        @foreach($upcomingSchedules ?? [] as $schedule)
            <div class="bg-white rounded-xl p-6 shadow-sm border-l-4
                @if($schedule->priority === 'high') border-red-500
                @elseif($schedule->priority === 'medium') border-purple-500
                @else border-blue-500
                @endif">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $schedule->title }}</h2>
                    <span class="text-sm text-gray-400">{{ $schedule->formatted_date }}</span>
                </div>
                <p class="text-gray-600 mt-2">{{ $schedule->description }}</p>
                <div class="flex items-center gap-2 mt-4">
                    <span class="px-3 py-1 text-xs rounded-full font-medium
                        @if($schedule->priority === 'high') bg-red-100 text-red-700
                        @elseif($schedule->priority === 'medium') bg-purple-100 text-purple-700
                        @else bg-blue-100 text-blue-700
                        @endif">
                        {{ ucfirst($schedule->priority) }} Priority
                    </span>
                    @if($schedule->time_range)
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                            {{ $schedule->time_range }}
                        </span>
                    @endif
                    <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">
                        Schedule
                    </span>
                </div>
            </div>
        @endforeach

        <!-- Public Announcements -->
        @forelse($announcements ?? [] as $announcement)
            <div class="bg-white rounded-xl p-6 shadow-sm border-l-4
                @if($announcement->category === 'important') border-red-500
                @elseif($announcement->category === 'notice') border-orange-500
                @elseif($announcement->category === 'update') border-blue-500
                @elseif($announcement->category === 'event') border-green-500
                @else border-gray-300
                @endif">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h2>
                    <span class="text-sm text-gray-400">{{ $announcement->time_ago }}</span>
                </div>
                <p class="text-gray-600 mt-2">{{ $announcement->content }}</p>
                <div class="flex items-center gap-2 mt-4">
                    <span class="px-3 py-1 text-xs rounded-full font-medium
                        @if($announcement->category === 'important') bg-red-100 text-red-700
                        @elseif($announcement->category === 'notice') bg-orange-100 text-orange-700
                        @elseif($announcement->category === 'update') bg-blue-100 text-blue-700
                        @elseif($announcement->category === 'event') bg-green-100 text-green-700
                        @endif">
                        {{ ucfirst($announcement->category) }}
                    </span>
                    <span class="px-3 py-1 text-xs rounded-full
                        @if($announcement->audience === 'parents') bg-purple-100 text-purple-700
                        @elseif($announcement->audience === 'teachers') bg-indigo-100 text-indigo-700
                        @elseif($announcement->audience === 'administrator') bg-teal-100 text-teal-700
                        @elseif($announcement->audience === 'principal') bg-pink-100 text-pink-700
                        @elseif($announcement->audience === 'supporting_staff') bg-cyan-100 text-cyan-700
                        @elseif($announcement->audience === 'faculty') bg-emerald-100 text-emerald-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        @if($announcement->audience === 'supporting_staff')
                            Supporting Staff
                        @elseif($announcement->audience === 'faculty')
                            Faculty
                        @else
                            {{ ucfirst($announcement->audience) }}
                        @endif
                    </span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl p-8 shadow-sm text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Announcements</h3>
                <p class="text-gray-600">There are no announcements to display at this time.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    const announcementFilterButton = document.getElementById('announcementFilterButton');
    const announcementFilterMenu = document.getElementById('announcementFilterMenu');
    const announcementFilterLabel = document.getElementById('announcementFilterLabel');

    announcementFilterButton.addEventListener('click', () => {
        announcementFilterMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', (event) => {
        if (!announcementFilterButton.contains(event.target) && !announcementFilterMenu.contains(event.target)) {
            announcementFilterMenu.classList.add('hidden');
        }
    });

    announcementFilterMenu.querySelectorAll('[data-filter]').forEach((item) => {
        item.addEventListener('click', () => {
            const filter = item.dataset.filter;
            announcementFilterLabel.textContent = filter;
            announcementFilterMenu.classList.add('hidden');

            // Redirect with filter parameter
            window.location.href = '{{ route("teacher.announcements") }}?filter=' + encodeURIComponent(filter);
        });
    });
</script>
@endsection
