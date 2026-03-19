@extends('layouts.ad-sidebar')

@section('title', 'Project Analytics')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Project Analytics</h1>
                <p class="text-gray-600 mt-1">Review project completion, budgets, and timelines.</p>
            </div>
        </div>

        <form method="GET" class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Statuses</option>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option }}" {{ $status === $option ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $option)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Projects</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalProjects) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Completed Projects</p>
            <p class="text-3xl font-bold text-green-600">{{ number_format($completedProjects) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Completion Rate</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($completionRate, 2) }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Target Budget</p>
            <p class="text-3xl font-bold text-gray-900">₱{{ number_format($totalTargetBudget, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Current Amount</p>
            <p class="text-3xl font-bold text-green-600">₱{{ number_format($totalCurrentAmount, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Project Summary</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Target Date</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Target Budget</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Current Amount</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($projects as $project)
                        @php
                            $progress = $project->target_budget > 0 ? round(($project->current_amount / $project->target_budget) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $project->project_name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $project->project_status)) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ optional($project->start_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ optional($project->target_completion_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($project->target_budget, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($project->current_amount, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ $progress }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-sm text-gray-500 text-center">No projects found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    </div>
</div>
@endsection
