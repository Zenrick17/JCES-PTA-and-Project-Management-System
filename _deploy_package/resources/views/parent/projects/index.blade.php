@extends('layouts.parent-sidebar')

@section('title', 'Projects')

@section('content')
<div class="space-y-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">PTA Projects</h2>
                <p class="text-sm text-gray-600 mt-1">Browse upcoming and previous school improvement projects</p>
            </div>
        </div>

        <form method="GET" class="mt-6 flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Search projects..."
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-md border border-gray-200 focus:ring-2 focus:ring-green-200 focus:border-green-400"
                />
            </div>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                Search
            </button>
        </form>
    </div>

    <!-- Upcoming Section -->
    <section class="space-y-4">
        <h3 class="text-base font-semibold text-gray-900">Upcoming</h3>
        <div class="space-y-4">
            @forelse ($upcomingProjects as $project)
                <a href="{{ route('parent.projects.show', $project->projectID) }}" class="block group">
                    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-5 flex flex-col md:flex-row gap-5">
                        <div class="w-28 h-28 bg-white rounded-lg overflow-hidden flex items-center justify-center">
                            <img src="{{ $project->parsed_details['photo'] ?? asset('images/logos/jces-logo.png') }}" alt="Project image" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-semibold text-gray-900 group-hover:text-green-700">{{ $project->project_name }}</h4>
                            <ul class="mt-2 space-y-1 text-sm text-gray-600">
                                <li>• Project Title: {{ $project->parsed_details['title'] ?? $project->project_name }}</li>
                                <li>• Date: {{ $project->parsed_details['date'] ?? optional($project->start_date)->format('F d, Y') . ' – ' . optional($project->target_completion_date)->format('F d, Y') }}</li>
                                <li>• Time: {{ $project->parsed_details['time'] ?? 'TBA' }}</li>
                                <li>• Venue: {{ $project->parsed_details['venue'] ?? 'To be announced' }}</li>
                                <li>• Objective: {{ $project->parsed_details['objective'] ?? $project->goals }}</li>
                                <li class="flex items-center gap-2">✅ Total Cost: ₱{{ number_format($project->target_budget, 2) }}</li>
                            </ul>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-lg shadow p-6 text-sm text-gray-500">No upcoming projects found.</div>
            @endforelse
        </div>
    </section>

    <!-- Previous Section -->
    <section class="space-y-4">
        <h3 class="text-base font-semibold text-gray-900">Previous</h3>
        <div class="space-y-4">
            @forelse ($previousProjects as $project)
                <a href="{{ route('parent.projects.show', $project->projectID) }}" class="block group">
                    <div class="bg-purple-50 border-l-4 border-purple-500 rounded-xl p-5 flex flex-col md:flex-row gap-5">
                        <div class="w-28 h-28 bg-white rounded-lg overflow-hidden flex items-center justify-center">
                            <img src="{{ $project->parsed_details['photo'] ?? asset('images/logos/jces-logo.png') }}" alt="Project image" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-semibold text-gray-900 group-hover:text-green-700">{{ $project->project_name }}</h4>
                            <ul class="mt-2 space-y-1 text-sm text-gray-600">
                                <li>• Project Title: {{ $project->parsed_details['title'] ?? $project->project_name }}</li>
                                <li>• Date: {{ $project->parsed_details['date'] ?? optional($project->start_date)->format('F d, Y') . ' – ' . optional($project->target_completion_date)->format('F d, Y') }}</li>
                                <li>• Time: {{ $project->parsed_details['time'] ?? 'TBA' }}</li>
                                <li>• Venue: {{ $project->parsed_details['venue'] ?? 'To be announced' }}</li>
                                <li>• Objective: {{ $project->parsed_details['objective'] ?? $project->goals }}</li>
                                <li class="flex items-center gap-2">✅ Total Cost: ₱{{ number_format($project->target_budget, 2) }}</li>
                            </ul>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-lg shadow p-6 text-sm text-gray-500">No previous projects found.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
