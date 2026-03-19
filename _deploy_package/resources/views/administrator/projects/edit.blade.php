@extends('layouts.ad-sidebar')

@section('title', 'Edit Project')

@section('content')
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
@endphp

<div class="w-full space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Project</h1>
        <p class="text-gray-600 mt-1">Update project details and status.</p>
    </div>

    <form method="POST" action="{{ route('administrator.projects.update', $project->projectID) }}" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Name</label>
                    <input type="text" name="project_name" value="{{ old('project_name', $project->project_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('description', $project->description) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Suggested format: Title: ... \nVenue: ... \nTime: ... \nPhoto: /storage/projects/... \nObjective: ...</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Goals</label>
                    <textarea name="goals" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('goals', $project->goals) }}</textarea>
                    <p class="mt-2 text-xs text-gray-500">Format: Goals: ... | Success Criteria: ...</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Target Budget</label>
                        <input type="number" step="0.01" name="target_budget" value="{{ old('target_budget', $project->target_budget) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Target Completion</label>
                        <input type="date" name="target_completion_date" value="{{ old('target_completion_date', optional($project->target_completion_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project Status</label>
                        <select name="project_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}" {{ old('project_status', $project->project_status) === $option ? 'selected' : '' }}>
                                    {{ $option === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $option)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Actual Completion Date</label>
                        <input type="date" name="actual_completion_date" value="{{ old('actual_completion_date', optional($project->actual_completion_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Project Summary</h3>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Current</span>
                            <span class="font-semibold text-green-700">₱{{ number_format($project->current_amount, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Target</span>
                            <span class="font-semibold text-gray-900">₱{{ number_format($project->target_budget, 2) }}</span>
                        </div>
                        @php
                            $progress = $project->target_budget > 0 ? min(100, round(($project->current_amount / $project->target_budget) * 100, 1)) : 0;
                        @endphp
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-900">Project Photo</h3>
                    <div class="mt-3">
                        @if ($photo)
                            <img src="{{ $photo }}" alt="Project photo" class="w-full h-40 object-cover rounded-md border">
                            <a href="{{ $photo }}" target="_blank" class="mt-2 inline-flex text-sm text-green-700 hover:text-green-800">Open image</a>
                        @else
                            <div class="w-full h-40 bg-gray-100 rounded-md flex items-center justify-center text-xs text-gray-500">No image available</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('administrator.projects.show', $project->projectID) }}" class="px-4 py-2 text-gray-700 border rounded-md">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Changes</button>
        </div>
    </form>
</div>
@endsection
