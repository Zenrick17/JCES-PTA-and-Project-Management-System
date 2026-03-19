@extends('layouts.parent-sidebar')

@section('title', 'Project Details')

@section('content')
<div class="space-y-6">
    <!-- Project Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $project->project_name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $project->project_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $project->project_status === 'created' ? 'Not Started' : ucfirst($project->project_status) }}
                    </span>
                </div>
                <p class="text-gray-600">{{ $project->description }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('parent.projects.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Back to Projects
                </a>
            </div>
        </div>
    </div>

    <!-- Progress and Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Total Raised</div>
            <div class="text-2xl font-bold text-gray-900">₱{{ number_format($contributionStats['total_amount'], 2) }}</div>
            <div class="text-sm text-gray-500 mt-2">Goal: ₱{{ number_format($project->target_budget, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Progress</div>
            <div class="text-2xl font-bold text-green-600">{{ $project->progress_percentage }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $project->progress_percentage }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Parent Support</div>
            <div class="text-2xl font-bold text-gray-900">{{ $contributionStats['unique_parents'] }}</div>
            <div class="text-sm text-gray-500 mt-2">Contributions: {{ $contributionStats['total_contributions'] }}</div>
        </div>
    </div>

    <!-- Project Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Project Goals</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $project->parsed_goals ?: $project->goals }}</p>
            </div>

            @if(!empty($project->parsed_criteria))
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Success Criteria</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $project->parsed_criteria }}</p>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Updates</h2>
                @if($recentUpdates->isEmpty())
                    <p class="text-gray-500">No updates yet.</p>
                @else
                    <div class="space-y-4">
                        @foreach($recentUpdates as $update)
                            <div class="border-l-4 border-green-500 pl-4">
                                <h3 class="font-semibold text-gray-900">{{ $update->update_title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $update->update_description }}</p>
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ $update->update_date->format('M d, Y') }} • {{ $update->updater?->name ?? 'Administrator' }}
                                </div>
                                @if($update->progress_percentage)
                                    <div class="text-xs text-green-600 mt-1">Progress: {{ $update->progress_percentage }}%</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Timeline -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Start Date</span>
                        <span class="text-sm font-medium text-gray-900">{{ optional($project->start_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Target Completion</span>
                        <span class="text-sm font-medium text-gray-900">{{ optional($project->target_completion_date)->format('M d, Y') }}</span>
                    </div>
                    @if($project->actual_completion_date)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="text-sm font-medium text-gray-900">{{ optional($project->actual_completion_date)->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">My Contributions</h2>

                @if($parentContributions->isEmpty())
                    <p class="text-sm text-gray-500">You have not contributed to this project yet.</p>
                @else
                    <div class="mb-4 pb-4 border-b border-gray-200">
                        <div class="text-sm text-gray-500">Your Total Contribution</div>
                        <div class="text-2xl font-bold text-gray-900">₱{{ number_format($parentContributions->sum('contribution_amount'), 2) }}</div>
                    </div>

                    <div class="space-y-3 max-h-72 overflow-y-auto">
                        @foreach($parentContributions as $contribution)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-semibold text-gray-900">₱{{ number_format($contribution->contribution_amount, 2) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ optional($contribution->contribution_date)->format('M d, Y h:i A') }}
                                        </div>
                                        @if($contribution->receipt_number)
                                            <div class="text-xs text-gray-500 mt-1">Receipt: {{ $contribution->receipt_number }}</div>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        {{ $contribution->payment_status === 'completed' ? 'bg-green-100 text-green-800' : ($contribution->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($contribution->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
