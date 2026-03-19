@extends('layouts.pr-sidebar')

@section('title', 'Reports Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reports Dashboard</h1>
                <p class="text-gray-600 mt-1">Monitor system activity and user behavior</p>
                <p class="text-sm text-gray-600 mt-2">All digital payments must be confirmed by administrators (treasurer) against actual received funds before they are marked as completed.</p>
            </div>
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Reconciliation Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Latest Financial Reconciliation</h2>
            @if($latestReconciliation)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $latestReconciliation->reconciliation_status === 'completed' ? 'bg-green-100 text-green-800' : ($latestReconciliation->reconciliation_status === 'discrepancy_found' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucwords(str_replace('_', ' ', $latestReconciliation->reconciliation_status)) }}
                </span>
            @endif
        </div>

        @if($latestReconciliation)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Period</p>
                    <p class="font-semibold text-gray-900">{{ $latestReconciliation->reconciliation_period }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Date Reconciled</p>
                    <p class="font-semibold text-gray-900">{{ \Illuminate\Support\Carbon::parse($latestReconciliation->reconciled_date)->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Discrepancy</p>
                    <p class="font-semibold {{ (float)$latestReconciliation->discrepancy_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₱{{ number_format($latestReconciliation->discrepancy_amount, 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Reconciled By</p>
                    <p class="font-semibold text-gray-900">{{ trim($latestReconciliation->reconciled_by_name) ?: 'N/A' }}</p>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500">No reconciliation records available yet.</p>
        @endif
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Recent Activity (7 Days)</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($recentActivityCount) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Failed Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed Actions (7 Days)</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($failedActionsCount) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Users (7 Days)</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($uniqueUsersCount) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Actions Chart -->
    @if(count($topActions) > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Actions (Last 7 Days)</h3>
        <div class="space-y-3">
            @foreach(array_slice($topActions, 0, 5, true) as $action => $count)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $action) }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ max(10, ($count / array_values($topActions)[0]) * 100) }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $count }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Activity Logs -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">User Activity Logs</h3>
                    <p class="text-sm text-gray-600">All user actions and system interactions</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Track all user activities with detailed information including timestamps, IP addresses, and action details.</p>
            <a href="{{ route('principal.reports.activity-logs') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                View Activity Logs
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

        <!-- Audit Logs -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Audit Logs</h3>
                    <p class="text-sm text-gray-600">Authentication and audit events</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Monitor login attempts, authentication failures, and audit-related activities for compliance.</p>
            <a href="{{ route('principal.reports.security-logs') }}" 
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                View Audit Logs
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

        <!-- User Activity Statistics -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">User Activity Stats</h3>
                    <p class="text-sm text-gray-600">Usage patterns and trends</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Analyze user behavior patterns, peak usage times, and activity trends with interactive charts.</p>
            <a href="{{ route('principal.reports.user-activity') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                View Activity Stats
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Analytics & Financial Reports -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Payments & Contributions -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 5a3 3 0 016 0v1h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h2V5zm2 1h2V5a1 1 0 10-2 0v1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Payments & Contributions</h3>
                    <p class="text-sm text-gray-600">Payment history and statuses</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Review payments, contribution status, and recent history.</p>
            <a href="{{ route('principal.reports.payments') }}" 
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                View Payments
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
        <!-- Enrollment Statistics -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V5h2v4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Enrollment Statistics</h3>
                    <p class="text-sm text-gray-600">Student counts and trends</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">View enrollment totals by academic year and grade level with active rates.</p>
            <a href="{{ route('principal.reports.enrollment') }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                View Enrollment Report
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

        <!-- Participation Report -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 7H7v6h6V7z"/>
                        <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v10H5V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Parent Participation</h3>
                    <p class="text-sm text-gray-600">Contributions by period</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Analyze parent participation by project and time period with totals.</p>
            <a href="{{ route('principal.reports.participation') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                View Participation Report
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

        <!-- Project Analytics -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Project Analytics</h3>
                    <p class="text-sm text-gray-600">Completion rates and budgets</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Track project performance, completion rates, and funding progress.</p>
            <a href="{{ route('principal.reports.project-analytics') }}" 
               class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                View Project Analytics
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v1a3 3 0 00-1 5.83V13a1 1 0 102 0v-1a3 3 0 001-5.83V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Financial Summary</h3>
                    <p class="text-sm text-gray-600">Transactions and totals</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Review payment totals by method and export summaries.</p>
            <a href="{{ route('principal.reports.financial-summary') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                View Financial Summary
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

        <!-- KPI Dashboard -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">KPI Dashboard</h3>
                    <p class="text-sm text-gray-600">Real-time performance metrics</p>
                </div>
            </div>
            <p class="text-gray-700 mb-4">Track key indicators across enrollment, projects, and finance.</p>
            <a href="{{ route('principal.reports.kpis') }}" 
               class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200">
                View KPI Dashboard
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
