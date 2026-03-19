@extends('layouts.ad-sidebar')

@section('title', 'Create Project')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New Project</h1>
        <p class="text-gray-600 mt-1">Define objectives, budgets, and timelines.</p>
    </div>

    <form method="POST" action="{{ route('administrator.projects.store') }}" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Project Name</label>
            <input type="text" name="project_name" value="{{ old('project_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Goals</label>
            <textarea name="goals" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('goals') }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Target Budget</label>
                <input type="number" step="0.01" name="target_budget" value="{{ old('target_budget') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Target Completion</label>
                <input type="date" name="target_completion_date" value="{{ old('target_completion_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Project Status</label>
            <select name="project_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @foreach ($statusOptions as $option)
                    <option value="{{ $option }}" {{ old('project_status', 'created') === $option ? 'selected' : '' }}>
                        {{ $option === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $option)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('administrator.projects.index') }}" class="px-4 py-2 text-gray-700 border rounded-md">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Create Project</button>
        </div>
    </form>
</div>
@endsection
