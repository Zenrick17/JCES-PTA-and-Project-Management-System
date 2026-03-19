@extends('layouts.pr-sidebar')

@section('title', 'KPI Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">KPI Dashboard</h1>
                <p class="text-gray-600 mt-1">Real-time performance indicators for key subsystems.</p>
            </div>
        </div>

        <form method="GET" class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Lookback (Days)</label>
                <input type="number" name="days" value="{{ $days }}" min="7" max="365" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Refresh KPIs</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Students</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['totalStudents']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Active Students</p>
            <p class="text-3xl font-bold text-green-600">{{ number_format($metrics['activeStudents']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Active Rate</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($metrics['activeRate'], 2) }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Parents</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($metrics['totalParents']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Participation Rate</p>
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($metrics['participationRate'], 2) }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Contributions</p>
            <p class="text-3xl font-bold text-green-600">₱{{ number_format($metrics['totalContributions'], 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Payments</p>
            <p class="text-3xl font-bold text-gray-900">₱{{ number_format($metrics['totalPayments'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Active Projects</p>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($metrics['activeProjects']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Project Completion Rate</p>
            <p class="text-3xl font-bold text-teal-600">{{ number_format($metrics['projectCompletionRate'], 2) }}%</p>
        </div>
    </div>
</div>
@endsection
