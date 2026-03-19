@extends('layouts.te-sidebar')

@section('title', 'Projects')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="w-full md:w-56">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Statuses</option>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option }}" {{ $status === $option ? 'selected' : '' }}>
                            {{ $option === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $option)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:flex-1">
                <label class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Project name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
            </div>
        </form>
    </div>

    <div class="mt-6">
        @php
            $today = \Carbon\Carbon::today();
            $upcoming = $projects->filter(function ($project) use ($today) {
                $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null;
                return $startDate ? $startDate->isSameDay($today) || $startDate->isAfter($today) : false;
            });
            $previous = $projects->filter(function ($project) use ($today) {
                $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null;
                return $startDate ? $startDate->isBefore($today) : false;
            });
        @endphp

        <div class="space-y-8">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming</h2>
                <div class="space-y-4">
                    @forelse ($upcoming as $project)
                        @php
                            $detailMap = [];
                            $lines = preg_split('/\r\n|\r|\n/', $project->description ?? '');
                            foreach ($lines as $line) {
                                if (strpos($line, ':') !== false) {
                                    [$key, $value] = array_map('trim', explode(':', $line, 2));
                                    if (!empty($key)) {
                                        $detailMap[$key] = $value;
                                    }
                                }
                            }
                            $photo = $detailMap['Photo'] ?? null;
                            $title = $detailMap['Title'] ?? $project->project_name;
                            $time = $detailMap['Time'] ?? null;
                            $venue = $detailMap['Venue'] ?? null;
                            $objective = $project->goals ?? null;
                        @endphp
                        <div class="block bg-slate-50 border-l-4 border-blue-500 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="w-32 h-32 bg-white rounded-md border flex items-center justify-center overflow-hidden">
                                    @if ($photo)
                                        <img src="{{ $photo }}" alt="{{ $project->project_name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs text-gray-400">No Image</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <a href="{{ route('teacher.projects.show', $project->projectID) }}" class="hover:underline">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $project->project_name }}</h3>
                                        </a>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 text-xs font-medium rounded 
                                                {{ $project->project_status === 'created' ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $project->project_status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $project->project_status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $project->project_status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                                {{ $project->project_status === 'archived' ? 'bg-gray-100 text-gray-500' : '' }}
                                                {{ $project->project_status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                            ">
                                                {{ $project->project_status === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $project->project_status)) }}
                                            </span>
                                            @if($project->project_status === 'created')
                                                <form method="POST" action="{{ route('teacher.projects.activate', $project->projectID) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700" onclick="return confirm('Activate this project for parent contributions?')">
                                                        Activate
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>Project Title: {{ $title }}</li>
                                        <li>Date: {{ optional($project->start_date)->format('F j, Y') }} - {{ optional($project->target_completion_date)->format('F j, Y') }}</li>
                                        @if ($time)
                                            <li>Time: {{ $time }}</li>
                                        @endif
                                        @if ($venue)
                                            <li>Venue: {{ $venue }}</li>
                                        @endif
                                        @if ($objective)
                                            <li>Objective: {{ $objective }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No upcoming projects.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Previous</h2>
                <div class="space-y-4">
                    @forelse ($previous as $project)
                        @php
                            $detailMap = [];
                            $lines = preg_split('/\r\n|\r|\n/', $project->description ?? '');
                            foreach ($lines as $line) {
                                if (strpos($line, ':') !== false) {
                                    [$key, $value] = array_map('trim', explode(':', $line, 2));
                                    if (!empty($key)) {
                                        $detailMap[$key] = $value;
                                    }
                                }
                            }
                            $photo = $detailMap['Photo'] ?? null;
                            $title = $detailMap['Title'] ?? $project->project_name;
                            $time = $detailMap['Time'] ?? null;
                            $venue = $detailMap['Venue'] ?? null;
                            $objective = $project->goals ?? null;
                        @endphp
                        <div class="block bg-purple-50 border-l-4 border-purple-500 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="w-32 h-32 bg-white rounded-md border flex items-center justify-center overflow-hidden">
                                    @if ($photo)
                                        <img src="{{ $photo }}" alt="{{ $project->project_name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs text-gray-400">No Image</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <a href="{{ route('teacher.projects.show', $project->projectID) }}" class="hover:underline">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $project->project_name }}</h3>
                                        </a>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 text-xs font-medium rounded 
                                                {{ $project->project_status === 'created' ? 'bg-gray-100 text-gray-700' : '' }}
                                                {{ $project->project_status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $project->project_status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ $project->project_status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                                {{ $project->project_status === 'archived' ? 'bg-gray-100 text-gray-500' : '' }}
                                                {{ $project->project_status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                            ">
                                                {{ $project->project_status === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $project->project_status)) }}
                                            </span>
                                            @if($project->project_status === 'created')
                                                <form method="POST" action="{{ route('teacher.projects.activate', $project->projectID) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700" onclick="return confirm('Activate this project for parent contributions?')">
                                                        Activate
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>Project Title: {{ $title }}</li>
                                        <li>Date: {{ optional($project->start_date)->format('F j, Y') }} - {{ optional($project->target_completion_date)->format('F j, Y') }}</li>
                                        @if ($time)
                                            <li>Time: {{ $time }}</li>
                                        @endif
                                        @if ($venue)
                                            <li>Venue: {{ $venue }}</li>
                                        @endif
                                        @if ($objective)
                                            <li>Objective: {{ $objective }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No previous projects.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    </div>
</div>
@endsection
