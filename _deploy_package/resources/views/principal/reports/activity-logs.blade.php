@extends('layouts.pr-sidebar')

@section('title', 'User Activity Logs')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Activity Logs</h1>
                <p class="text-gray-600 mt-1">All user actions and system interactions with detailed audit trail</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('principal.reports') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    Back to Reports
                </a>
                <a href="{{ route('principal.reports.export', request()->query()) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('principal.reports.activity-logs') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                    <option value="{{ $user->userID }}" {{ request('user_id') == $user->userID ? 'selected' : '' }}>
                        {{ $user->first_name }} {{ $user->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <input type="text" name="action" value="{{ request('action') }}" placeholder="Search actions..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">IP Address</label>
                <input type="text" name="ip_address" value="{{ request('ip_address') }}" placeholder="IP Address..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="success" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">All</option>
                    <option value="true" {{ request('success') === 'true' ? 'selected' : '' }}>Success</option>
                    <option value="false" {{ request('success') === 'false' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="md:col-span-2 lg:col-span-6 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('principal.reports.activity-logs') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Activity Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Activity Records</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $logs->total() }} total records found</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $log->timestamp->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->timestamp->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->user)
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-green-600 font-medium text-xs">
                                        {{ substr($log->user->first_name, 0, 1) }}{{ substr($log->user->last_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $log->user->email }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-sm text-gray-500">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if(str_contains($log->action, 'login')) bg-blue-100 text-blue-800
                                @elseif(str_contains($log->action, 'create')) bg-green-100 text-green-800
                                @elseif(str_contains($log->action, 'update')) bg-yellow-100 text-yellow-800
                                @elseif(str_contains($log->action, 'delete')) bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($log->table_affected)
                            <div class="text-xs text-gray-500">Table: {{ $log->table_affected }}</div>
                            @endif
                            @if($log->record_id)
                            <div class="text-xs text-gray-500">Record ID: {{ $log->record_id }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->success)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Success
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Failed
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="showLogDetails({{ $log->logID }})" 
                                    class="text-blue-600 hover:text-blue-900">
                                View Details
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-lg font-medium">No activity logs found</p>
                            <p class="text-sm">Try adjusting your filters to see more results.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Log Details Modal -->
<div id="logDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-90vh overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Activity Log Details</h3>
                    <button onclick="closeLogDetails()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="logDetailsContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetails(logId) {
    const modal = document.getElementById('logDetailsModal');
    const content = document.getElementById('logDetailsContent');
    
    // Show loading state
    content.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2 text-gray-600">Loading details...</p></div>';
    modal.classList.remove('hidden');
    
    // Find the log data from the current page
    const logData = @json($logs->items()).find(log => log.logID === logId);
    
    if (logData) {
        content.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Basic Information</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Timestamp</dt>
                                <dd class="text-sm text-gray-900">${new Date(logData.timestamp).toLocaleString()}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">User</dt>
                                <dd class="text-sm text-gray-900">${logData.user ? logData.user.first_name + ' ' + logData.user.last_name : 'System'}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Action</dt>
                                <dd class="text-sm text-gray-900">${logData.action}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Success</dt>
                                <dd class="text-sm text-gray-900">${logData.success ? 'Yes' : 'No'}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Technical Details</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                                <dd class="text-sm text-gray-900">${logData.ip_address || 'N/A'}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Session ID</dt>
                                <dd class="text-sm text-gray-900 break-all">${logData.session_id || 'N/A'}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Table Affected</dt>
                                <dd class="text-sm text-gray-900">${logData.table_affected || 'N/A'}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Record ID</dt>
                                <dd class="text-sm text-gray-900">${logData.record_id || 'N/A'}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                ${logData.user_agent ? `
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">User Agent</h4>
                    <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">${logData.user_agent}</p>
                </div>
                ` : ''}
                
                ${logData.old_values ? `
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Old Values</h4>
                    <pre class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg overflow-x-auto">${JSON.stringify(logData.old_values, null, 2)}</pre>
                </div>
                ` : ''}
                
                ${logData.new_values ? `
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">New Values</h4>
                    <pre class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg overflow-x-auto">${JSON.stringify(logData.new_values, null, 2)}</pre>
                </div>
                ` : ''}
                
                ${logData.error_message ? `
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Error Message</h4>
                    <p class="text-sm text-red-600 bg-red-50 p-3 rounded-lg">${logData.error_message}</p>
                </div>
                ` : ''}
            </div>
        `;
    }
}

function closeLogDetails() {
    document.getElementById('logDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('logDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogDetails();
    }
});
</script>
@endsection
