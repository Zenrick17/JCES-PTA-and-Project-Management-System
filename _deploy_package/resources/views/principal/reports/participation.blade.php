@extends('layouts.pr-sidebar')

@section('title', 'Parent Participation Report')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Parent Participation Report</h1>
                <p class="text-gray-600 mt-1">Analyze contributions across projects and time periods.</p>
            </div>
        </div>

        <form method="GET" class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Project</label>
                <select name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Projects</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->projectID }}" {{ (string) $projectId === (string) $project->projectID ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Contributions</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalContributionCount) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Amount</p>
            <p class="text-3xl font-bold text-green-600">₱{{ number_format($totalContributionAmount, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Unique Parents</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($uniqueParents) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contribution Details</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($contributions as $contribution)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ optional($contribution->contribution_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                {{ $contribution->parent ? $contribution->parent->first_name . ' ' . $contribution->parent->last_name : 'Unknown Parent' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $contribution->project?->project_name ?? 'Unknown Project' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($contribution->contribution_amount, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $contribution->payment_method)) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $contribution->receipt_number ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-sm text-gray-500 text-center">No contributions found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $contributions->links() }}
        </div>
    </div>
</div>
@endsection
