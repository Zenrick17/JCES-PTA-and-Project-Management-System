@extends('layouts.ad-sidebar')

@section('title', 'Project Details')

@section('content')
<div class="space-y-6">
    {{-- Project Header --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $project->project_name }}</h1>
                <div class="flex items-center gap-3 mt-2">
                    @php
                        $statusColors = [
                            'created' => 'bg-gray-100 text-gray-700',
                            'active' => 'bg-green-100 text-green-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'archived' => 'bg-purple-100 text-purple-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                        $statusLabel = $project->project_status === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $project->project_status));
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$project->project_status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $statusLabel }}
                    </span>
                    @if($project->start_date && $project->target_completion_date)
                        @php
                            $daysRemaining = max(0, now()->diffInDays($project->target_completion_date, false));
                        @endphp
                        <span class="text-xs text-gray-500">
                            @if($daysRemaining > 0)
                                {{ $daysRemaining }} days remaining
                            @elseif(in_array($project->project_status, ['completed', 'archived']))
                                Completed
                            @else
                                Overdue by {{ abs(now()->diffInDays($project->target_completion_date, false)) }} days
                            @endif
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($project->project_status === 'created')
                    <form method="POST" action="{{ route('administrator.projects.activate', $project->projectID) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors" onclick="return confirm('Activate this project for parent contributions?')">
                            Activate Project
                        </button>
                    </form>
                @endif
                @if(in_array($project->project_status, ['active', 'in_progress']))
                    <form method="POST" action="{{ route('administrator.projects.request-closure', $project->projectID) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 transition-colors" onclick="return confirm('Request closure for this project?')">
                            Request Closure
                        </button>
                    </form>
                @endif
                <a href="{{ route('administrator.projects.edit', $project->projectID) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Edit Project</a>
            </div>
        </div>
    </div>

    {{-- Progress Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Overall Progress --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Overall Progress</h3>
                <span class="text-2xl font-bold text-blue-600">{{ number_format($latestProgress, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 ease-out
                    {{ $latestProgress >= 100 ? 'bg-green-500' : ($latestProgress >= 50 ? 'bg-blue-500' : 'bg-amber-500') }}"
                    style="width: {{ min($latestProgress, 100) }}%">
                    @if($latestProgress >= 15)
                        <span class="text-[10px] text-white font-bold flex items-center justify-center h-full">{{ number_format($latestProgress, 1) }}%</span>
                    @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Based on latest project update</p>
        </div>

        {{-- Fund Progress --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Fund Collection</h3>
                <span class="text-2xl font-bold text-green-600">{{ number_format($fundProgress, 1) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 ease-out
                    {{ $fundProgress >= 100 ? 'bg-green-500' : ($fundProgress >= 50 ? 'bg-emerald-500' : 'bg-amber-500') }}"
                    style="width: {{ min($fundProgress, 100) }}%">
                    @if($fundProgress >= 15)
                        <span class="text-[10px] text-white font-bold flex items-center justify-center h-full">{{ number_format($fundProgress, 1) }}%</span>
                    @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">₱{{ number_format($project->current_amount, 2) }} of ₱{{ number_format($project->target_budget, 2) }}</p>
        </div>

        {{-- Milestone Progress --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Milestones</h3>
                <span class="text-2xl font-bold text-purple-600">{{ $completedMilestones }}/{{ $totalMilestones }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div class="h-full bg-purple-500 rounded-full transition-all duration-500 ease-out"
                    style="width: {{ $milestoneProgress }}%">
                    @if($milestoneProgress >= 15)
                        <span class="text-[10px] text-white font-bold flex items-center justify-center h-full">{{ number_format($milestoneProgress, 1) }}%</span>
                    @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                @if($totalMilestones === 0)
                    No milestones defined yet
                @else
                    {{ number_format($milestoneProgress, 1) }}% milestones completed
                @endif
            </p>
        </div>
    </div>

    {{-- Project Details + Timeline --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Overview</h2>
            <p class="text-gray-700 mb-4">{{ $project->description }}</p>
            <h3 class="text-sm font-semibold text-gray-700">Goals</h3>
            <p class="text-gray-700 mt-1">{{ $project->goals }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Timeline</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500">Start Date</p>
                    <p class="text-sm font-medium text-gray-700">{{ optional($project->start_date)->format('M d, Y') }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500">Target Completion</p>
                    <p class="text-sm font-medium text-gray-700">{{ optional($project->target_completion_date)->format('M d, Y') }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-500">Actual Completion</p>
                    <p class="text-sm font-medium text-gray-700">{{ optional($project->actual_completion_date)->format('M d, Y') ?? 'In Progress' }}</p>
                </div>
                @if($project->start_date && $project->target_completion_date)
                    @php
                        $totalDays = max($project->start_date->diffInDays($project->target_completion_date), 1);
                        $elapsed = min($project->start_date->diffInDays(now()), $totalDays);
                        $timeProgress = round(($elapsed / $totalDays) * 100, 1);
                    @endphp
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs text-gray-500">Time Elapsed</p>
                            <p class="text-xs font-medium text-gray-600">{{ number_format(min($timeProgress, 100), 1) }}%</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-full bg-indigo-400 rounded-full" style="width: {{ min($timeProgress, 100) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Milestones Section --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Project Milestones</h2>
            <button onclick="document.getElementById('milestone-form').classList.toggle('hidden')"
                class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-sm rounded-md hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Milestone
            </button>
        </div>

        {{-- Add Milestone Form --}}
        <div id="milestone-form" class="{{ $errors->any() ? '' : 'hidden' }} mb-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <ul class="text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('administrator.projects.milestones.store', $project->projectID) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Milestone Title</label>
                    <input type="text" name="title" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="e.g. Phase 1 Complete" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Date</label>
                    <input type="date" name="target_date" 
                        min="{{ optional($project->start_date)->format('Y-m-d') }}" 
                        max="{{ optional($project->target_completion_date)->format('Y-m-d') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Between {{ optional($project->start_date)->format('M d, Y') }} - {{ optional($project->target_completion_date)->format('M d, Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500" placeholder="Optional description">
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors text-sm">Save Milestone</button>
                </div>
            </form>
        </div>

        {{-- Milestone Timeline --}}
        @if($milestones->count() > 0)
            <div class="relative">
                <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                @if($milestoneProgress > 0)
                    <div class="absolute left-5 top-0 w-0.5 bg-purple-500 transition-all duration-500" style="height: {{ $milestoneProgress }}%"></div>
                @endif

                <div class="space-y-4">
                    @foreach($milestones as $milestone)
                        <div class="relative flex items-start gap-4 pl-12">
                            <div class="absolute left-3 top-1 w-5 h-5 rounded-full border-2 flex items-center justify-center
                                {{ $milestone->is_completed ? 'bg-purple-500 border-purple-500' : 'bg-white border-gray-300' }}">
                                @if($milestone->is_completed)
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @endif
                            </div>

                            <div class="flex-1 bg-gray-50 rounded-lg p-4 border {{ $milestone->is_completed ? 'border-purple-200 bg-purple-50' : 'border-gray-200' }}">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-3">
                                        <h4 class="text-sm font-semibold {{ $milestone->is_completed ? 'text-purple-700 line-through' : 'text-gray-900' }}">{{ $milestone->title }}</h4>
                                        @if($milestone->is_completed)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Completed</span>
                                        @elseif($milestone->target_date && $milestone->target_date->isToday())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Due Today</span>
                                        @elseif($milestone->target_date && $milestone->target_date->isPast())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Overdue</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('administrator.projects.milestones.toggle', [$project->projectID, $milestone->milestoneID]) }}">
                                            @csrf
                                            <button type="submit" class="text-xs px-3 py-1 rounded-md border transition-colors
                                                {{ $milestone->is_completed ? 'border-gray-300 text-gray-600 hover:bg-gray-100' : 'border-purple-300 text-purple-600 hover:bg-purple-50' }}">
                                                {{ $milestone->is_completed ? 'Undo' : 'Complete' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('administrator.projects.milestones.destroy', [$project->projectID, $milestone->milestoneID]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Delete this milestone?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if($milestone->description)
                                    <p class="text-xs text-gray-600 mt-1">{{ $milestone->description }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                    @if($milestone->target_date)
                                        <span>Target: {{ $milestone->target_date->format('M d, Y') }}</span>
                                    @endif
                                    @if($milestone->completed_date)
                                        <span class="text-green-600">Completed: {{ $milestone->completed_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <p class="text-sm text-gray-500">No milestones defined yet. Add milestones to track project progress.</p>
            </div>
        @endif
    </div>

    {{-- Add Project Update --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Add Project Update</h2>
        <form method="POST" action="{{ route('administrator.projects.updates.store', $project->projectID) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Update Title</label>
                    <input type="text" name="update_title" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Milestone Achieved</label>
                    <input type="text" name="milestone_achieved" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="progress_percentage" id="progress-input"
                            value="{{ $latestProgress }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-8"
                            oninput="document.getElementById('progress-preview').style.width = Math.min(this.value || 0, 100) + '%'">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div id="progress-preview" class="h-full bg-blue-500 rounded-full transition-all duration-300" style="width: {{ min($latestProgress, 100) }}%"></div>
                    </div>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Update Description</label>
                    <textarea name="update_description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                </div>
                <div class="md:col-span-4 flex items-center justify-end">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Post Update</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Project Updates Timeline --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Project Updates</h2>
        <div class="space-y-4">
            @forelse ($updates as $update)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $update->update_title }}</h3>
                                @if($update->milestone_achieved)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        {{ $update->milestone_achieved }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">{{ optional($update->update_date)->format('M d, Y \a\t h:i A') }}</p>
                        </div>
                        <form method="POST" action="{{ route('administrator.projects.updates.destroy', [$project->projectID, $update->updateID]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700 transition-colors" onclick="return confirm('Delete this update?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                    <p class="text-sm text-gray-700 mt-2">{{ $update->update_description }}</p>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex-1">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-full rounded-full transition-all duration-300
                                    {{ $update->progress_percentage >= 100 ? 'bg-green-500' : ($update->progress_percentage >= 50 ? 'bg-blue-500' : 'bg-amber-500') }}"
                                    style="width: {{ min($update->progress_percentage, 100) }}%"></div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-gray-600 w-12 text-right">{{ number_format($update->progress_percentage, 1) }}%</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-sm text-gray-500">No updates posted yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Contributions --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Contributions</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
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
                            <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($contribution->contribution_amount, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $contribution->payment_method)) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                @if($contribution->receipt_number)
                                    <a href="{{ route('administrator.payments.receipt', $contribution->contributionID) }}" target="_blank" class="text-green-600 hover:text-green-700 text-sm font-medium">Print</a>
                                @else
                                    <span class="text-gray-400 text-sm">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-sm text-gray-500 text-center">No contributions recorded.</td>
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
