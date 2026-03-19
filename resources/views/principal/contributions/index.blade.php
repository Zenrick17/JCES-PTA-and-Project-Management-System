@extends('layouts.pr-sidebar')

@section('title', 'Payments')

@section('content')
<div class="space-y-6" x-data="paymentsManager()">
    <!-- Statistics Cards - Per Active Project -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($projectStats as $stat)
            <div class="bg-white rounded-lg shadow p-5 border-l-4 {{ $stat['status'] === 'active' ? 'border-green-500' : 'border-blue-500' }}">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700 truncate" title="{{ $stat['project_name'] }}">
                        {{ Str::limit($stat['project_name'], 30) }}
                    </h3>
                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $stat['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($stat['status']) }}
                    </span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs text-gray-500">Contributions</span>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($stat['contribution_count']) }}</span>
                    </div>
                    <div class="flex items-baseline justify-between">
                        <span class="text-xs text-gray-500">Collected</span>
                        <span class="text-lg font-bold text-green-600">₱{{ number_format($stat['total_amount'], 2) }}</span>
                    </div>
                    <div class="flex items-baseline justify-between pt-1 border-t border-gray-200">
                        <span class="text-xs text-gray-500">Target</span>
                        <span class="text-sm font-medium text-gray-700">₱{{ number_format($stat['target_budget'], 2) }}</span>
                    </div>
                    @php
                        $percentage = $stat['target_budget'] > 0 ? ($stat['total_amount'] / $stat['target_budget']) * 100 : 0;
                    @endphp
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-500">Progress</span>
                            <span class="font-medium text-gray-700">{{ number_format($percentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow p-6 text-center">
                <p class="text-sm text-gray-500">No active projects found</p>
            </div>
        @endforelse
    </div>

    <!-- Manual Payment Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Process Manual Payment</h2>

        <!-- Parent Selection with Search -->
        <div class="mb-6" x-data="{ open: false }" @click.away="open = false">
            <label for="parentSearch" class="block text-sm font-medium text-gray-700 mb-2">Select Parent</label>
            <div class="relative">
                <input
                    type="text"
                    id="parentSearch"
                    @input="filterParents($event.target.value)"
                    @focus="showParentDropdown = true; open = true"
                    @click="open = true"
                    placeholder="Search parent by name or email..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 pr-10"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div
                    x-show="open && showParentDropdown && filteredParents.length > 0"
                    class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                    <template x-for="parent in filteredParents" :key="parent.parentID">
                        <div
                            @click="selectParent(parent); open = false"
                            class="px-4 py-2 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                            <span class="font-medium text-gray-900" x-text="parent.last_name + ', ' + parent.first_name"></span>
                            <span class="text-sm text-gray-500 ml-2" x-text="'(' + parent.email + ')'"></span>
                        </div>
                    </template>
                </div>
            </div>
            <input type="hidden" id="selectedParentId" x-model="selectedParent" />
        </div>

        <!-- Bills Display -->
        <div x-show="selectedParent && bills.length > 0" class="mb-6">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Unpaid Bills for <span id="selectedParentName" class="text-green-600"></span></h4>
                <div class="space-y-2" id="billsContainer"></div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Total Amount: <span id="manualTotal" class="text-xl font-bold text-green-600" x-text="'₱' + calculateTotal().toFixed(2)">₱0.00</span>
                </div>
                <button @click="openPaymentModal" :disabled="selectedBills.length === 0"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-all shadow-md hover:shadow-lg">
                    Process Payment
                </button>
            </div>
        </div>

        <div x-show="selectedParent && bills.length === 0 && !loading" class="mb-6">
            <p class="text-sm text-gray-500">No active project bills found for this parent.</p>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="bg-white rounded-lg shadow">
        <form method="GET" id="filterForm" class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="relative w-full md:flex-1">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    x-model="searchQuery"
                    @input.debounce.300ms="filterTable()"
                    value="{{ request('search') }}"
                    placeholder="Search parent or project"
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-md border border-gray-200 focus:ring-2 focus:ring-green-200 focus:border-green-400 outline-none transition-all"
                />
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Status Filter -->
                <div class="relative">
                    <select name="status" id="statusFilter" x-model="statusFilter" @change="filterTable()" class="appearance-none px-4 py-2 pr-8 text-sm font-medium bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-300 focus:ring-offset-1 focus:outline-none cursor-pointer min-w-[110px] transition-colors">
                        <option value="">Status</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                    <span class="absolute inset-y-0 right-2 flex items-center pointer-events-none text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
                <!-- Date Range Filter -->
                <div class="relative">
                    <select name="date_range" id="dateRangeFilter" x-model="dateRangeFilter" @change="filterTable()" class="appearance-none px-4 py-2 pr-8 text-sm font-medium bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-300 focus:ring-offset-1 focus:outline-none cursor-pointer min-w-[120px] transition-colors">
                        <option value="">Date Range</option>
                        <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ request('date_range') === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="this_year" {{ request('date_range') === 'this_year' ? 'selected' : '' }}>This Year</option>
                    </select>
                    <span class="absolute inset-y-0 right-2 flex items-center pointer-events-none text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
                <!-- School Year Filter -->
                <div class="relative">
                    <select name="school_year" id="schoolYearFilter" x-model="schoolYearFilter" @change="filterTable()" class="appearance-none px-4 py-2 pr-8 text-sm font-medium bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-300 focus:ring-offset-1 focus:outline-none cursor-pointer min-w-[130px] transition-colors">
                        <option value="">All Years</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy }}" {{ request('school_year') === $sy ? 'selected' : '' }}>S.Y {{ $sy }}</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-2 flex items-center pointer-events-none text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
                <!-- Clear Button -->
                <button
                    type="button"
                    x-show="searchQuery || statusFilter || dateRangeFilter || schoolYearFilter"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    @click="clearFilters()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-50 hover:text-gray-800 hover:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:outline-none transition-all"
                >
                    Clear
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table id="paymentsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-40">Parent</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 w-28">Required</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 w-28">Paid</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-48">Project</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-40">Processed By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-28">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-24">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($contributions as $contribution)
                        @php
                            $status = $contribution->payment_status;
                            $statusLabel = $status === 'completed' ? 'Paid' : ($status === 'pending' ? 'Pending' : 'Refunded');
                            $statusClass = $status === 'completed' ? 'bg-green-100 text-green-800' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            $requiredPayment = isset($projectPayments[$contribution->projectID]) ? $projectPayments[$contribution->projectID] : 0;
                            $parentName = $contribution->parent ? $contribution->parent->first_name . ' ' . $contribution->parent->last_name : 'Unknown Parent';
                            $projectName = $contribution->project?->project_name ?? 'Unknown Project';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors"
                            data-parent="{{ $parentName }}"
                            data-project="{{ $projectName }}"
                            data-status="{{ $status }}"
                            data-date="{{ optional($contribution->contribution_date)->format('Y-m-d') }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="selected[]" value="{{ $contribution->contributionID }}" class="row-checkbox w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer" />
                                <span class="ml-2 text-sm text-green-600 hover:text-green-700 font-medium cursor-pointer">
                                    {{ $parentName }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-500">₱{{ number_format($requiredPayment, 2) }}</td>
                            <td class="px-3 py-3 text-sm font-medium text-gray-900">₱{{ number_format($contribution->contribution_amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $projectName }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($contribution->processedBy)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $contribution->processedBy->first_name }} {{ $contribution->processedBy->last_name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ optional($contribution->contribution_date)->format('m-d-Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $status === 'completed' ? 'bg-green-100 text-green-700' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        // Check if receipt image exists
                                        $receiptImageExists = false;
                                        $receiptImagePath = '';
                                        foreach(['jpg', 'jpeg', 'png', 'gif'] as $ext) {
                                            if(\Illuminate\Support\Facades\Storage::disk('public')->exists('receipt_img/' . $contribution->contributionID . '.' . $ext)) {
                                                $receiptImageExists = true;
                                                $receiptImagePath = \Illuminate\Support\Facades\Storage::url('receipt_img/' . $contribution->contributionID . '.' . $ext) . '?t=' . time();
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if($receiptImageExists)
                                        <button type="button"
                                                onclick="showVerifyModal('{{ addslashes($receiptImagePath) }}', '{{ $contribution->contributionID }}', '{{ $status }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 text-xs font-semibold rounded-lg transition-colors border border-blue-200"
                                                title="Verify Payment">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Verify
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-400 text-xs rounded-lg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            No Image
                                        </span>
                                    @endif
                                    @if($contribution->receipt_number)
                                        <a href="{{ route('principal.contributions.receipt', $contribution->contributionID) }}" target="_blank" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                            Print
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">N/A</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-sm text-gray-500 text-center">
                                No payment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div id="resultsCount" class="text-sm text-gray-600">
                    @if($contributions->count() > 0)
                        Showing <span class="font-medium text-green-600">{{ $contributions->firstItem() ?? 0 }}</span> to <span class="font-medium text-green-600">{{ $contributions->lastItem() ?? 0 }}</span> of <span class="font-medium text-green-600">{{ $contributions->total() }}</span> results
                    @else
                        No results found
                    @endif
                </div>
                <div class="flex items-center gap-1">
                    @if($contributions->count() > 0)
                        @if ($contributions->onFirstPage())
                            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&lt;</span>
                        @else
                            <a href="{{ $contributions->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">&lt;</a>
                        @endif
                        @php
                            $currentPage = $contributions->currentPage();
                            $lastPage = $contributions->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                        @endphp
                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span class="px-3 py-1 text-sm font-semibold text-white bg-green-600 rounded-md">{{ $page }}</span>
                            @else
                                <a href="{{ $contributions->url($page) }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endfor
                        @if ($contributions->hasMorePages())
                            <a href="{{ $contributions->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">&gt;</a>
                        @else
                            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&gt;</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="showModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>

            <!-- Modal Content -->
            <div class="bg-white rounded-2xl w-full max-w-2xl relative z-10" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.15);">
                <!-- Close Button -->
                <button @click="showModal = false" class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition-all z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="p-8">
                    <!-- Header -->
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Process Manual Payment</h2>
                        <p class="text-sm text-gray-600">Enter payment details and upload proof of payment</p>
                    </div>

                    <!-- Parent Details -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6" x-show="selectedParentInfo">
                        <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Parent Details
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide mb-0.5">Full Name</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="selectedParentInfo ? selectedParentInfo.last_name + ', ' + selectedParentInfo.first_name : ''"></p>
                            </div>
                            <div>
                                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide mb-0.5">Email</p>
                                <p class="text-sm text-gray-700 break-all" x-text="selectedParentInfo ? selectedParentInfo.email : ''"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Payment Summary</h3>
                        <div class="space-y-2 mb-3">
                            <template x-for="bill in selectedBills" :key="bill.projectID">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-700" x-text="bill.project_name"></span>
                                    <span class="text-sm font-semibold text-gray-900" x-text="'₱' + parseFloat(bill.amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                </div>
                            </template>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-700">Total</span>
                            <span class="text-lg font-bold text-gray-900" x-text="'₱' + calculateTotal().toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form @submit.prevent="submitPayment" enctype="multipart/form-data">
                        <div class="space-y-4 mb-6">
                            <!-- Payment Method -->
                            <div>
                                <p class="block text-sm font-medium text-gray-700 mb-2">Payment Method</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <label for="manualPaymentMethodCash" class="cursor-pointer">
                                        <input id="manualPaymentMethodCash" name="payment_method" type="radio" x-model="paymentMethod" value="cash" class="sr-only peer">
                                        <div class="px-4 py-3 border-2 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 peer-checked:border-gray-500 peer-checked:bg-gray-50 peer-checked:scale-105 peer-checked:shadow-lg border-gray-200 bg-white">
                                            <div class="w-7 h-7 bg-gray-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-700">Cash</span>
                                        </div>
                                    </label>
                                    <label for="manualPaymentMethodGcash" class="cursor-pointer">
                                        <input id="manualPaymentMethodGcash" name="payment_method" type="radio" x-model="paymentMethod" value="gcash" class="sr-only peer">
                                        <div class="px-4 py-3 border-2 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:scale-105 peer-checked:shadow-lg border-gray-200 bg-white">
                                            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-semibold text-blue-700">GCash</span>
                                        </div>
                                    </label>
                                    <label for="manualPaymentMethodMaya" class="cursor-pointer">
                                        <input id="manualPaymentMethodMaya" name="payment_method" type="radio" x-model="paymentMethod" value="maya" class="sr-only peer">
                                        <div class="px-4 py-3 border-2 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:scale-105 peer-checked:shadow-lg border-gray-200 bg-white">
                                            <div class="w-7 h-7 bg-green-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-semibold text-green-700">Maya</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="manualPaymentNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                                <textarea id="manualPaymentNotes"
                                          name="notes"
                                          x-model="notes"
                                          rows="2"
                                          placeholder="Additional payment details..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                            </div>

                            <!-- Proof of Payment Upload -->
                            <div>
                                <label for="manualProofImage" class="block text-sm font-medium text-gray-700 mb-2">Proof of Payment</label>
                                <label for="manualProofImage" class="block cursor-pointer">
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-500 hover:bg-green-50 transition-all"
                                         :class="{'border-green-500 bg-green-50': proofImage}">
                                        <template x-if="!proofImage">
                                            <div>
                                                <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <p class="text-sm font-medium text-gray-700 mb-1">Click to upload payment proof</p>
                                                <p class="text-xs text-gray-500">Accepted file types: JPG, JPEG, PNG, GIF (max 5MB)</p>
                                            </div>
                                        </template>
                                        <template x-if="proofImage">
                                            <div>
                                                <svg class="w-10 h-10 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-sm font-medium text-green-700 mb-1" x-text="proofImage.name"></p>
                                                <p class="text-xs text-gray-500">Click to change file</p>
                                            </div>
                                        </template>
                                    </div>
                                    <input id="manualProofImage" name="proof_image" type="file" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif" @change="handleProofImageChange($event)" class="hidden">
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                :disabled="submitting || !paymentMethod || !proofImage"
                                class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-base font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-show="!submitting">Submit Payment</span>
                            <span x-show="submitting">Processing...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function paymentsManager() {
    const allowedProofMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const maxProofFileSize = 5 * 1024 * 1024;

    return {
        // Filter state
        searchQuery: '{{ request("search") }}',
        statusFilter: '{{ request("status") }}',
        dateRangeFilter: '{{ request("date_range") }}',
        schoolYearFilter: '{{ request("school_year") }}',

        // Payment state
        selectedParent: null,
        selectedParentInfo: null,
        bills: [],
        selectedBills: [],
        loading: false,
        showModal: false,
        paymentMethod: '',
        proofImage: null,
        notes: '',
        submitting: false,

        // Parent search state
        allParents: {!! json_encode($parents->map(function($parent) {
            $accountEmail = $parent->user->email ?? null;
            $profileEmail = $parent->email ?? null;

            return [
                'parentID' => $parent->parentID,
                'first_name' => $parent->user->first_name ?: ($parent->first_name ?? ''),
                'last_name' => $parent->user->last_name ?: ($parent->last_name ?? ''),
                'email' => $accountEmail ?: ($profileEmail ?? ''),
                'search_email' => trim(implode(' ', array_filter([$accountEmail, $profileEmail]))),
            ];
        })->filter(function($p) {
            return !empty($p['first_name']) || !empty($p['last_name']);
        })->values()) !!},
        filteredParents: [],
        showParentDropdown: false,

        init() {
            this.filteredParents = this.allParents;
        },

        filterParents(search) {
            if (!search || search.trim() === '') {
                this.filteredParents = this.allParents;
                this.showParentDropdown = true;
                return;
            }

            const searchLower = search.toLowerCase();
            this.filteredParents = this.allParents.filter(parent => {
                const fullName = `${parent.last_name}, ${parent.first_name}`.toLowerCase();
                const email = (parent.email || '').toLowerCase();
                const searchEmail = (parent.search_email || '').toLowerCase();
                return fullName.includes(searchLower) || email.includes(searchLower) || searchEmail.includes(searchLower);
            });
            this.showParentDropdown = true;
        },

        selectParent(parent) {
            document.getElementById('parentSearch').value = `${parent.last_name}, ${parent.first_name} (${parent.email})`;
            document.getElementById('selectedParentName').textContent = `${parent.first_name} ${parent.last_name}`;
            this.selectedParentInfo = parent;
            this.showParentDropdown = false;
            this.loadParentBills(parent.parentID);
        },

        handleProofImageChange(event) {
            const file = event?.target?.files?.[0] || null;

            if (!file) {
                this.proofImage = null;
                return;
            }

            if (!allowedProofMimeTypes.includes(file.type)) {
                alert('Unsupported file type. Please upload JPG, JPEG, PNG, or GIF only.');
                event.target.value = '';
                this.proofImage = null;
                return;
            }

            if (file.size > maxProofFileSize) {
                alert('File is too large. Maximum allowed size is 5MB.');
                event.target.value = '';
                this.proofImage = null;
                return;
            }

            this.proofImage = file;
        },

        filterTable() {
            const form = document.getElementById('filterForm');
            form.submit();
        },

        clearFilters() {
            this.searchQuery = '';
            this.statusFilter = '';
            this.dateRangeFilter = '';
            this.schoolYearFilter = '';
            window.location.href = '{{ route("principal.contributions.index") }}';
        },

        async loadParentBills(parentId) {
            if (!parentId) {
                this.selectedParent = null;
                this.bills = [];
                this.selectedBills = [];
                document.getElementById('billsContainer').innerHTML = '';
                return;
            }

            this.selectedParent = parentId;
            this.loading = true;
            this.selectedBills = [];

            try {
                const response = await fetch(`/principal/contributions/parent-bills/${parentId}`);
                const data = await response.json();

                if (data.success) {
                    this.bills = data.bills;
                    this.displayParentBills(data.bills);
                }
            } catch (error) {
                console.error('Error loading parent bills:', error);
                alert('Failed to load parent bills');
            } finally {
                this.loading = false;
            }
        },

        displayParentBills(bills) {
            const container = document.getElementById('billsContainer');
            container.innerHTML = '';

            bills.forEach(bill => {
                const billDiv = document.createElement('label');
                billDiv.className = `flex items-center justify-between p-3 bg-white rounded-lg border transition-all ${bill.is_pending ? 'border-amber-300 bg-amber-50' : 'border-gray-200 hover:border-green-300 cursor-pointer'}`;
                billDiv.innerHTML = `
                    <div class="flex items-center gap-3">
                        ${bill.is_pending ? `
                            <span class="inline-flex px-2 py-1 text-[10px] font-semibold uppercase tracking-wide rounded-full bg-amber-100 text-amber-700">Pending</span>
                        ` : `
                            <input type="checkbox"
                                   value="${bill.projectID}"
                                   data-project-name="${bill.project_name}"
                                   data-amount="${bill.amount}"
                                   onchange="Alpine.store('paymentManager').toggleBill(this)"
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        `}
                        <div>
                            <span class="text-sm text-gray-900 block">${bill.project_name}</span>
                            ${bill.is_pending ? `<span class="text-xs text-amber-700">${bill.status_note || 'Pending - waiting for approval'}</span>` : ''}
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">₱${parseFloat(bill.amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                `;
                container.appendChild(billDiv);
            });
        },

        toggleBill(checkbox) {
            const bill = {
                projectID: checkbox.value,
                project_name: checkbox.dataset.projectName,
                amount: parseFloat(checkbox.dataset.amount)
            };

            if (checkbox.checked) {
                this.selectedBills.push(bill);
            } else {
                this.selectedBills = this.selectedBills.filter(b => b.projectID !== bill.projectID);
            }
        },

        calculateTotal() {
            return this.selectedBills.reduce((sum, bill) => sum + parseFloat(bill.amount), 0);
        },

        openPaymentModal() {
            this.showModal = true;
        },

        async submitPayment() {
            if (!this.paymentMethod || !this.proofImage) {
                alert('Please fill in all required fields');
                return;
            }

            this.submitting = true;

            const formData = new FormData();
            formData.append('parent_id', this.selectedParent);
            formData.append('payment_method', this.paymentMethod);
            formData.append('notes', this.notes);
            formData.append('proof_image', this.proofImage);

            this.selectedBills.forEach((bill, index) => {
                formData.append(`project_ids[${index}]`, bill.projectID);
                formData.append(`amounts[${index}]`, bill.amount);
            });

            try {
                const response = await fetch('{{ route("principal.contributions.submit-manual") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('Payment processed successfully!\nReceipt #: ' + data.receipt_number);
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Payment submission error:', error);
                alert('An error occurred while processing the payment');
            } finally {
                this.submitting = false;
            }
        }
    }
}

// Store for checkbox toggling
document.addEventListener('alpine:init', () => {
    Alpine.store('paymentManager', {
        component: null,

        toggleBill(checkbox) {
            const event = new CustomEvent('toggle-bill', { detail: { checkbox } });
            window.dispatchEvent(event);
        }
    });
});

// Handle toggle event
window.addEventListener('toggle-bill', (event) => {
    const component = Alpine.$data(document.querySelector('[x-data="paymentsManager()"]'));
    if (component) {
        component.toggleBill(event.detail.checkbox);
    }
});

// Verify Payment Modal Functions
function showVerifyModal(imagePath, contributionId, currentStatus) {
    console.log('Verify Modal - Image Path:', imagePath);
    console.log('Verify Modal - Contribution ID:', contributionId);

    // Remove existing modal if any
    const existingModal = document.getElementById('verifyPaymentModal');
    if (existingModal) existingModal.remove();

    const isPaid = currentStatus === 'completed';
    const isPending = currentStatus === 'pending';
    const isRefunded = currentStatus === 'refunded';

    // Create modal
    const modal = document.createElement('div');
    modal.id = 'verifyPaymentModal';
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="fixed inset-0 bg-black/50" onclick="closeVerifyModal()"></div>
        <div class="bg-white rounded-lg max-w-3xl w-full relative z-10 overflow-hidden shadow-2xl">
            <!-- Header -->
            <div class="bg-white px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-semibold text-gray-900">Payment Verification</h3>
                    <span class="text-xs text-gray-400">Ref #${contributionId}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full ${isPaid ? 'bg-green-100 text-green-700' : (isPending ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')}">
                        <span class="w-1.5 h-1.5 rounded-full ${isPaid ? 'bg-green-500' : (isPending ? 'bg-amber-500' : 'bg-red-500')}"></span>
                        ${isPaid ? 'Paid' : (isPending ? 'Pending' : 'Refunded')}
                    </span>
                    <button onclick="closeVerifyModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Main Content - Side by Side -->
            <div class="flex">
                <!-- Left: Receipt Image -->
                <div class="w-1/2 p-4 bg-gray-50 border-r border-gray-200">
                    <p class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Receipt Image</p>
                    <div class="bg-white rounded-lg border border-gray-200 p-2 flex items-center justify-center" style="height: 280px; overflow: auto;">
                        <img id="receiptImagePreview" alt="Payment Receipt" class="max-w-full max-h-full object-contain rounded" style="display: block;">
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <a href="${imagePath}" download="receipt_${contributionId}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </a>
                        <a href="${imagePath}" target="_blank" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Open Full
                        </a>
                    </div>
                </div>

                <!-- Right: Verification Actions -->
                <div class="w-1/2 p-4 bg-white">
                    <p class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Update Status</p>

                    <div class="space-y-2">
                        <!-- Approve Button -->
                        <button onclick="updatePaymentStatus(${contributionId}, 'completed')"
                                class="w-full flex items-center gap-3 p-3 rounded-lg border-2 transition-all ${isPaid ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-500 hover:bg-green-50'}"
                                ${isPaid ? 'disabled' : ''}>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 ${isPaid ? 'bg-green-500' : 'bg-gray-100'}">
                                <svg class="w-5 h-5 ${isPaid ? 'text-white' : 'text-gray-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="block text-sm font-semibold ${isPaid ? 'text-green-700' : 'text-gray-700'}">Approve Payment</span>
                                <span class="block text-xs ${isPaid ? 'text-green-600' : 'text-gray-500'}">Mark as Paid</span>
                            </div>
                            ${isPaid ? '<svg class="w-5 h-5 text-green-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' : ''}
                        </button>

                        <!-- Hold Button -->
                        <button onclick="updatePaymentStatus(${contributionId}, 'pending')"
                                class="w-full flex items-center gap-3 p-3 rounded-lg border-2 transition-all ${isPending ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-amber-500 hover:bg-amber-50'}"
                                ${isPending ? 'disabled' : ''}>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 ${isPending ? 'bg-amber-500' : 'bg-gray-100'}">
                                <svg class="w-5 h-5 ${isPending ? 'text-white' : 'text-gray-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="block text-sm font-semibold ${isPending ? 'text-amber-700' : 'text-gray-700'}">Hold for Review</span>
                                <span class="block text-xs ${isPending ? 'text-amber-600' : 'text-gray-500'}">Mark as Pending</span>
                            </div>
                            ${isPending ? '<svg class="w-5 h-5 text-amber-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' : ''}
                        </button>

                        <!-- Reject Button -->
                        <button onclick="updatePaymentStatus(${contributionId}, 'refunded')"
                                class="w-full flex items-center gap-3 p-3 rounded-lg border-2 transition-all ${isRefunded ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-500 hover:bg-red-50'}"
                                ${isRefunded ? 'disabled' : ''}>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 ${isRefunded ? 'bg-red-500' : 'bg-gray-100'}">
                                <svg class="w-5 h-5 ${isRefunded ? 'text-white' : 'text-gray-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <div class="text-left">
                                <span class="block text-sm font-semibold ${isRefunded ? 'text-red-700' : 'text-gray-700'}">Reject Payment</span>
                                <span class="block text-xs ${isRefunded ? 'text-red-600' : 'text-gray-500'}">Mark as Refunded</span>
                            </div>
                            ${isRefunded ? '<svg class="w-5 h-5 text-red-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' : ''}
                        </button>
                    </div>

                    <!-- Close Button -->
                    <button onclick="closeVerifyModal()" class="w-full mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    // Set image source after modal is in DOM
    const imgElement = document.getElementById('receiptImagePreview');
    if (imgElement) {
        imgElement.src = imagePath;
    }
}

function closeVerifyModal() {
    const modal = document.getElementById('verifyPaymentModal');
    if (modal) modal.remove();
    document.body.style.overflow = '';
}

function updatePaymentStatus(contributionId, status) {
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/principal/contributions/${contributionId}`;

    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    // Add method spoofing for PUT
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);

    // Add status
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'payment_status';
    statusInput.value = status;
    form.appendChild(statusInput);

    document.body.appendChild(form);
    form.submit();
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVerifyModal();
    }
});
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
