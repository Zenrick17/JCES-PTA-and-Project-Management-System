@extends('layouts.ad-sidebar')

@section('title', 'Payments')

@section('content')

{{-- Flash error alert --}}
@if(session('error'))
<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="mb-4 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm"
    role="alert"
>
    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div class="flex-1">
        <p class="font-semibold">Invalid Status Change</p>
        <p class="mt-0.5">{{ session('error') }}</p>
    </div>
    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600" aria-label="Dismiss">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
@endif

<div class="space-y-6" x-data="paymentsManager()">
    <!-- Manual Payment Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Process Manual Payment</h2>

        <!-- Parent Selection with Search -->
        <div class="mb-6" x-data="{ open: false }" @click.away="open = false">
            <label for="adminParentSearch" class="block text-sm font-medium text-gray-700 mb-2">Select Parent</label>
            <div class="relative">
                <input
                    type="text"
                    id="adminParentSearch"
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
            <input type="hidden" id="adminSelectedParentId" x-model="selectedParent" />
        </div>

        <!-- Bills Display -->
        <div x-show="selectedParent && bills.length > 0" class="mb-6">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Unpaid Bills for <span id="adminSelectedParentName" class="text-green-600"></span></h4>
                <div class="space-y-2" id="adminBillsContainer"></div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Total Amount: <span class="text-xl font-bold text-green-600" x-text="'₱' + calculateTotal().toFixed(2)">₱0.00</span>
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
                <!-- Status Filter - Primary action style -->
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
                <!-- Date Range Filter - Primary action style -->
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
                <!-- School Year Filter - Primary action style -->
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
                <!-- Clear Button - Secondary/Ghost style -->
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
                    @php
                        // Sample data for demonstration (30 entries)
                        $samplePayments = [
                            ['name' => 'Anna Garcia', 'phone' => '09456789012', 'address' => 'Davao City', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 500, 'date' => '2026-01-27 01:59:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340233'],
                            ['name' => 'Maria Santos', 'phone' => '09123456789', 'address' => 'Manila City', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 300, 'date' => '2026-01-27 09:30:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340234'],
                            ['name' => 'Jose Reyes', 'phone' => '09234567890', 'address' => 'Quezon City', 'project' => 'Community and Parent Involvement', 'required' => 200, 'paid' => 200, 'date' => '2026-01-26 14:20:00', 'status' => 'completed', 'method' => 'Cash', 'txn' => 'TXN9450340235'],
                            ['name' => 'Elena Cruz', 'phone' => '09345678901', 'address' => 'Cebu City', 'project' => 'School Supplies Drive', 'required' => 150, 'paid' => 150, 'date' => '2026-01-26 10:15:00', 'status' => 'completed', 'method' => 'Bank Transfer', 'txn' => 'TXN9450340236'],
                            ['name' => 'Roberto Dela Cruz', 'phone' => '09456789012', 'address' => 'Makati City', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 0, 'date' => '2026-01-25 16:45:00', 'status' => 'pending', 'method' => 'Pending', 'txn' => 'TXN9450340237'],
                            ['name' => 'Carmen Fernandez', 'phone' => '09567890123', 'address' => 'Pasig City', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 300, 'date' => '2026-01-25 11:30:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340238'],
                            ['name' => 'Pedro Mendoza', 'phone' => '09678901234', 'address' => 'Taguig City', 'project' => 'Community and Parent Involvement', 'required' => 200, 'paid' => 100, 'date' => '2026-01-24 09:00:00', 'status' => 'pending', 'method' => 'Cash', 'txn' => 'TXN9450340239'],
                            ['name' => 'Rosa Villanueva', 'phone' => '09789012345', 'address' => 'Paranaque City', 'project' => 'School Supplies Drive', 'required' => 150, 'paid' => 150, 'date' => '2026-01-24 14:00:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340240'],
                            ['name' => 'Antonio Ramos', 'phone' => '09890123456', 'address' => 'Las Pinas City', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 500, 'date' => '2026-01-23 08:30:00', 'status' => 'completed', 'method' => 'Bank Transfer', 'txn' => 'TXN9450340241'],
                            ['name' => 'Lucia Torres', 'phone' => '09901234567', 'address' => 'Muntinlupa City', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 0, 'date' => '2026-01-23 15:45:00', 'status' => 'refunded', 'method' => 'Refunded', 'txn' => 'TXN9450340242'],
                            ['name' => 'Miguel Gonzales', 'phone' => '09112345678', 'address' => 'Caloocan City', 'project' => 'Community and Parent Involvement', 'required' => 200, 'paid' => 200, 'date' => '2026-01-22 10:00:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340243'],
                            ['name' => 'Teresa Aquino', 'phone' => '09223456789', 'address' => 'Valenzuela City', 'project' => 'School Supplies Drive', 'required' => 150, 'paid' => 150, 'date' => '2026-01-22 13:30:00', 'status' => 'completed', 'method' => 'Cash', 'txn' => 'TXN9450340244'],
                            ['name' => 'Ricardo Bautista', 'phone' => '09334567890', 'address' => 'Malabon City', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 250, 'date' => '2026-01-21 09:15:00', 'status' => 'pending', 'method' => 'GCASH', 'txn' => 'TXN9450340245'],
                            ['name' => 'Sofia Castillo', 'phone' => '09445678901', 'address' => 'Navotas City', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 300, 'date' => '2026-01-21 16:00:00', 'status' => 'completed', 'method' => 'Bank Transfer', 'txn' => 'TXN9450340246'],
                            ['name' => 'Fernando Jimenez', 'phone' => '09556789012', 'address' => 'San Juan City', 'project' => 'Community and Parent Involvement', 'required' => 200, 'paid' => 200, 'date' => '2026-01-20 11:45:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340247'],
                            ['name' => 'Isabel Morales', 'phone' => '09667890123', 'address' => 'Mandaluyong City', 'project' => 'School Supplies Drive', 'required' => 150, 'paid' => 0, 'date' => '2026-01-20 08:00:00', 'status' => 'refunded', 'method' => 'Refunded', 'txn' => 'TXN9450340248'],
                            ['name' => 'Carlos Navarro', 'phone' => '09778901234', 'address' => 'Marikina City', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 500, 'date' => '2026-01-19 14:30:00', 'status' => 'completed', 'method' => 'Cash', 'txn' => 'TXN9450340249'],
                            ['name' => 'Patricia Ocampo', 'phone' => '09889012345', 'address' => 'Pasay City', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 300, 'date' => '2026-01-19 10:20:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340250'],
                            ['name' => 'Daniel Pascual', 'phone' => '09990123456', 'address' => 'Batangas City', 'project' => 'Community and Parent Involvement', 'required' => 200, 'paid' => 100, 'date' => '2026-01-18 15:00:00', 'status' => 'pending', 'method' => 'Bank Transfer', 'txn' => 'TXN9450340251'],
                            ['name' => 'Gloria Quizon', 'phone' => '09101234567', 'address' => 'Laguna', 'project' => 'School Supplies Drive', 'required' => 150, 'paid' => 150, 'date' => '2026-01-18 09:45:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340252'],
                            ['name' => 'Manuel Rivera', 'phone' => '09212345678', 'address' => 'Cavite City', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 500, 'date' => '2026-01-17 12:30:00', 'status' => 'completed', 'method' => 'Cash', 'txn' => 'TXN9450340253'],
                            ['name' => 'Angelica Salazar', 'phone' => '09323456789', 'address' => 'Bulacan', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 0, 'date' => '2026-01-17 16:15:00', 'status' => 'pending', 'method' => 'Pending', 'txn' => 'TXN9450340254'],
                            ['name' => 'Eduardo Tan', 'phone' => '09434567890', 'address' => 'Pampanga', 'project' => 'Community and Parent Involvement', 'required' => 200, 'paid' => 200, 'date' => '2026-01-16 10:00:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340255'],
                            ['name' => 'Victoria Uy', 'phone' => '09545678901', 'address' => 'Tarlac City', 'project' => 'School Supplies Drive', 'required' => 150, 'paid' => 150, 'date' => '2026-01-16 14:45:00', 'status' => 'completed', 'method' => 'Bank Transfer', 'txn' => 'TXN9450340256'],
                            ['name' => 'Benjamin Vera', 'phone' => '09656789012', 'address' => 'Zambales', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 500, 'date' => '2026-01-15 08:30:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340257'],
                            ['name' => 'Cristina Wong', 'phone' => '09767890123', 'address' => 'Bataan', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 150, 'date' => '2026-01-15 11:00:00', 'status' => 'pending', 'method' => 'Cash', 'txn' => 'TXN9450340258'],
                            ['name' => 'Andres Yap', 'phone' => '09878901234', 'address' => 'Nueva Ecija', 'project' => 'Community and Parent Involvement', 'required' => 200, 'paid' => 200, 'date' => '2026-01-14 15:30:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340259'],
                            ['name' => 'Maricel Zamora', 'phone' => '09989012345', 'address' => 'Pangasinan', 'project' => 'School Supplies Drive', 'required' => 150, 'paid' => 0, 'date' => '2026-01-14 09:00:00', 'status' => 'refunded', 'method' => 'Refunded', 'txn' => 'TXN9450340260'],
                            ['name' => 'Lorenzo Abella', 'phone' => '09190123456', 'address' => 'La Union', 'project' => 'Fun Run for a Cause', 'required' => 500, 'paid' => 500, 'date' => '2026-01-13 13:15:00', 'status' => 'completed', 'method' => 'Bank Transfer', 'txn' => 'TXN9450340261'],
                            ['name' => 'Beatriz Borja', 'phone' => '09291234567', 'address' => 'Ilocos Norte', 'project' => 'Fundraising Projects', 'required' => 300, 'paid' => 300, 'date' => '2026-01-13 10:45:00', 'status' => 'completed', 'method' => 'GCASH', 'txn' => 'TXN9450340262'],
                        ];
                    @endphp

                    @forelse ($contributions as $index => $contribution)
                        @php
                            $status = $contribution->payment_status;
                            $statusLabel = $status === 'completed' ? 'Paid' : ($status === 'pending' ? 'Pending' : 'Refunded');
                            $statusClass = $status === 'completed' ? 'bg-green-100 text-green-800' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                            $requiredPayment = isset($projectPayments[$contribution->projectID]) ? $projectPayments[$contribution->projectID] : 0;
                            $sampleIndex = $index % count($samplePayments);
                            $sample = $samplePayments[$sampleIndex];
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
                                        <a href="{{ route('administrator.payments.receipt', $contribution->contributionID) }}" target="_blank" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                            Print
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-sm">N/A</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Show sample data when no contributions exist -->
                        @foreach($samplePayments as $index => $payment)
                            @php
                                $status = $payment['status'];
                                $statusLabel = $status === 'completed' ? 'Paid' : ($status === 'pending' ? 'Pending' : 'Refunded');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors"
                                data-parent="{{ $payment['name'] }}"
                                data-project="{{ $payment['project'] }}"
                                data-status="{{ $status }}"
                                data-date="{{ \Carbon\Carbon::parse($payment['date'])->format('Y-m-d') }}">
                                <td class="px-6 py-3">
                                    <input type="checkbox" name="selected[]" value="{{ $index + 1 }}" class="row-checkbox w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer" />
                                    <span class="ml-2 text-sm text-green-600 hover:text-green-700 font-medium cursor-pointer">{{ $payment['name'] }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">₱{{ number_format($payment['required'], 2) }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">₱{{ number_format($payment['paid'], 2) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ $payment['project'] }}</td>
                                <td class="px-6 py-3 text-sm text-gray-400">—</td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($payment['date'])->format('m-d-Y') }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $status === 'completed' ? 'bg-green-100 text-green-700' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-400 text-xs rounded-lg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            No Image
                                        </span>
                                        <a href="#" class="text-green-600 hover:text-green-700 text-sm font-medium">Print</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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
                        Showing <span class="font-medium text-green-600">1</span> to <span class="font-medium text-green-600">30</span> of <span class="font-medium text-green-600">30</span> results
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
                    @else
                        <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&lt;</span>
                        <span class="px-3 py-1 text-sm font-semibold text-white bg-green-600 rounded-md">1</span>
                        <span class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md">2</span>
                        <span class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md">3</span>
                        <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&gt;</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Status Modal -->
    <div x-show="showEditModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @click.self="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Payment Status</h3>
                </div>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form :action="'/administrator/payments/' + editContributionId" method="POST" class="px-6 py-5">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" :class="editStatus === 'completed' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                            <input type="radio" name="payment_status" value="completed" x-model="editStatus" class="w-4 h-4 text-green-600 focus:ring-green-500">
                            <span class="ml-3 flex items-center gap-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                <span class="text-sm text-gray-600">Payment has been received</span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" :class="editStatus === 'pending' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200'">
                            <input type="radio" name="payment_status" value="pending" x-model="editStatus" class="w-4 h-4 text-yellow-600 focus:ring-yellow-500">
                            <span class="ml-3 flex items-center gap-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                <span class="text-sm text-gray-600">Awaiting payment</span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" :class="editStatus === 'refunded' ? 'border-red-500 bg-red-50' : 'border-gray-200'">
                            <input type="radio" name="payment_status" value="refunded" x-model="editStatus" class="w-4 h-4 text-red-600 focus:ring-red-500">
                            <span class="ml-3 flex items-center gap-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Refunded</span>
                                <span class="text-sm text-gray-600">Payment has been rejected/refunded</span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Archive Modal - Step 1 -->
    <div x-show="showArchiveModal && !showArchiveConfirm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @click.self="showArchiveModal = false"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Archive</h3>
                <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to Archive this post?</p>
                <div class="flex gap-3">
                    <button type="button" @click="showArchiveModal = false" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" @click="showArchiveConfirm = true" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        Archive
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Modal - Step 2 (Confirmation) -->
    <div x-show="showArchiveModal && showArchiveConfirm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @click.self="closeArchiveModal()"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 transform transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-gray-600 text-center mb-4">Enter "Archive" to Confirm</p>
                <input
                    type="text"
                    x-model="archiveConfirmText"
                    placeholder="Archive"
                    class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-200 focus:border-yellow-400 text-center mb-4"
                />
                <div class="flex gap-3">
                    <button type="button" @click="closeArchiveModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="confirmArchive()"
                        :disabled="archiveConfirmText !== 'Archive'"
                        :class="archiveConfirmText === 'Archive' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-300 cursor-not-allowed'"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-lg">
                        Archive
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div x-show="showReceiptModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         @click.self="showReceiptModal = false"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all max-h-[90vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div id="receiptContent" class="p-6">
                <div class="text-center mb-4">
                    <p class="text-xs text-gray-500 tracking-widest">*** OFFICIAL RECEIPT ***</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-2">JCES PTA</h2>
                    <p class="text-sm text-gray-600">J. Cruz Sr. Elementary School</p>
                    <p class="text-sm text-gray-600">Parent-Teacher Association</p>
                </div>
                <div class="border-t-2 border-dashed border-gray-300 my-4"></div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">TXN #:</span>
                        <span class="font-medium text-gray-900" x-text="receiptData.txn"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date:</span>
                        <span class="font-medium text-gray-900" x-text="receiptData.date"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Payment:</span>
                        <span class="font-medium text-gray-900" x-text="receiptData.method"></span>
                    </div>
                </div>
                <div class="border-t-2 border-dashed border-gray-300 my-4"></div>
                <div class="mb-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">PAID BY:</p>
                    <p class="font-semibold text-gray-900" x-text="receiptData.name"></p>
                    <p class="text-sm text-gray-600" x-text="receiptData.phone"></p>
                    <p class="text-sm text-gray-600" x-text="receiptData.address"></p>
                </div>
                <div class="mb-4">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">ITEMS:</p>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-900" x-text="receiptData.project"></span>
                        <span class="font-semibold text-gray-900">₱<span x-text="formatCurrency(receiptData.amount)"></span></span>
                    </div>
                </div>
                <div class="border-t-2 border-dashed border-gray-300 my-4"></div>
                <div class="flex justify-between items-center py-2">
                    <span class="font-bold text-gray-900">TOTAL</span>
                    <span class="font-bold text-lg text-gray-900">₱<span x-text="formatCurrency(receiptData.amount)"></span></span>
                </div>
                <div class="text-center mt-6">
                    <p class="text-sm text-green-600">Thank you for your contribution!</p>
                    <div class="border-t-2 border-dashed border-gray-300 my-4"></div>
                    <p class="text-xs text-gray-500">This serves as your Official Receipt</p>
                    <p class="text-xs text-gray-500">Please keep for your records</p>
                </div>
            </div>
            <div class="px-6 pb-6 space-y-3">
                <button @click="downloadReceipt()" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Receipt
                </button>
                <button @click="showReceiptModal = false" class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="showPaymentModal = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showPaymentModal = false"></div>
            <div class="bg-white rounded-2xl w-full max-w-2xl relative z-10" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.15);">
                <button @click="showPaymentModal = false" class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition-all z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div class="p-8">
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
                            <div>
                                <p class="block text-sm font-medium text-gray-700 mb-2">Payment Method</p>
                                <div class="grid grid-cols-3 gap-3">
                                    <label for="adminPayMethodCash" class="cursor-pointer">
                                        <input id="adminPayMethodCash" name="payment_method" type="radio" x-model="paymentMethod" value="cash" class="sr-only peer">
                                        <div class="px-4 py-3 border-2 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 peer-checked:border-gray-500 peer-checked:bg-gray-50 peer-checked:scale-105 peer-checked:shadow-lg border-gray-200 bg-white">
                                            <div class="w-7 h-7 bg-gray-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/></svg>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-700">Cash</span>
                                        </div>
                                    </label>
                                    <label for="adminPayMethodGcash" class="cursor-pointer">
                                        <input id="adminPayMethodGcash" name="payment_method" type="radio" x-model="paymentMethod" value="gcash" class="sr-only peer">
                                        <div class="px-4 py-3 border-2 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:scale-105 peer-checked:shadow-lg border-gray-200 bg-white">
                                            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                                            </div>
                                            <span class="text-sm font-semibold text-blue-700">GCash</span>
                                        </div>
                                    </label>
                                    <label for="adminPayMethodMaya" class="cursor-pointer">
                                        <input id="adminPayMethodMaya" name="payment_method" type="radio" x-model="paymentMethod" value="maya" class="sr-only peer">
                                        <div class="px-4 py-3 border-2 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:scale-105 peer-checked:shadow-lg border-gray-200 bg-white">
                                            <div class="w-7 h-7 bg-green-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                                            </div>
                                            <span class="text-sm font-semibold text-green-700">Maya</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label for="adminPaymentNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                                <textarea id="adminPaymentNotes" name="notes" x-model="notes" rows="2" placeholder="Additional payment details..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                            </div>
                            <div>
                                <label for="adminProofImage" class="block text-sm font-medium text-gray-700 mb-2">Proof of Payment</label>
                                <label for="adminProofImage" class="block cursor-pointer">
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-500 hover:bg-green-50 transition-all" :class="{'border-green-500 bg-green-50': proofImage}">
                                        <template x-if="!proofImage">
                                            <div>
                                                <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <p class="text-sm font-medium text-gray-700 mb-1">Click to upload payment proof</p>
                                                <p class="text-xs text-gray-500">Accepted: JPG, JPEG, PNG, GIF (max 5MB)</p>
                                            </div>
                                        </template>
                                        <template x-if="proofImage">
                                            <div>
                                                <svg class="w-10 h-10 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <p class="text-sm font-medium text-green-700 mb-1" x-text="proofImage.name"></p>
                                                <p class="text-xs text-gray-500">Click to change file</p>
                                            </div>
                                        </template>
                                    </div>
                                    <input id="adminProofImage" name="proof_image" type="file" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif" @change="handleProofImageChange($event)" class="hidden">
                                </label>
                            </div>
                        </div>
                        <button type="submit" :disabled="submitting || !paymentMethod || !proofImage"
                                class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-base font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
    const allowedAdminProofMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    const maxAdminProofFileSize = 5 * 1024 * 1024;

    function paymentsManager() {
        return {
            // Manual payment state
            selectedParent: null,
            selectedParentInfo: null,
            bills: [],
            selectedBills: [],
            loading: false,
            showPaymentModal: false,
            paymentMethod: '',
            proofImage: null,
            notes: '',
            submitting: false,
            allParents: {!! json_encode($parents->map(function($parent) {
                $accountEmail = $parent->user->email ?? null;
                $profileEmail = $parent->email ?? null;
                return [
                    'parentID' => $parent->parentID,
                    'first_name' => $parent->user->first_name ?: ($parent->first_name ?? ''),
                    'last_name' => $parent->user->last_name ?: ($parent->last_name ?? ''),
                    'email' => $accountEmail ?: ($profileEmail ?? ''),
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
                    return fullName.includes(searchLower) || email.includes(searchLower);
                });
                this.showParentDropdown = true;
            },

            selectParent(parent) {
                document.getElementById('adminParentSearch').value = `${parent.last_name}, ${parent.first_name} (${parent.email})`;
                document.getElementById('adminSelectedParentName').textContent = `${parent.first_name} ${parent.last_name}`;
                this.selectedParentInfo = parent;
                this.showParentDropdown = false;
                this.loadParentBills(parent.parentID);
            },

            handleProofImageChange(event) {
                const file = event?.target?.files?.[0] || null;
                if (!file) { this.proofImage = null; return; }
                if (!allowedAdminProofMimeTypes.includes(file.type)) {
                    alert('Unsupported file type. Please upload JPG, JPEG, PNG, or GIF only.');
                    event.target.value = '';
                    this.proofImage = null;
                    return;
                }
                if (file.size > maxAdminProofFileSize) {
                    alert('File is too large. Maximum allowed size is 5MB.');
                    event.target.value = '';
                    this.proofImage = null;
                    return;
                }
                this.proofImage = file;
            },

            async loadParentBills(parentId) {
                if (!parentId) { this.selectedParent = null; this.bills = []; this.selectedBills = []; return; }
                this.selectedParent = parentId;
                this.loading = true;
                this.selectedBills = [];
                try {
                    const response = await fetch(`/administrator/payments/parent-bills/${parentId}`);
                    const data = await response.json();
                    if (data.success) { this.bills = data.bills; this.displayParentBills(data.bills); }
                } catch (error) {
                    console.error('Error loading parent bills:', error);
                    alert('Failed to load parent bills');
                } finally {
                    this.loading = false;
                }
            },

            displayParentBills(bills) {
                const container = document.getElementById('adminBillsContainer');
                container.innerHTML = '';
                bills.forEach(bill => {
                    const billDiv = document.createElement('label');
                    billDiv.className = `flex items-center justify-between p-3 bg-white rounded-lg border transition-all ${bill.is_pending ? 'border-amber-300 bg-amber-50' : 'border-gray-200 hover:border-green-300 cursor-pointer'}`;
                    billDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            ${bill.is_pending ? `<span class="inline-flex px-2 py-1 text-[10px] font-semibold uppercase tracking-wide rounded-full bg-amber-100 text-amber-700">Pending</span>` :
                            `<input type="checkbox" value="${bill.projectID}" data-project-name="${bill.project_name}" data-amount="${bill.amount}" onchange="Alpine.store('adminPaymentMgr').toggleBill(this)" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">`}
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
                const bill = { projectID: checkbox.value, project_name: checkbox.dataset.projectName, amount: parseFloat(checkbox.dataset.amount) };
                if (checkbox.checked) { this.selectedBills.push(bill); }
                else { this.selectedBills = this.selectedBills.filter(b => b.projectID !== bill.projectID); }
            },

            calculateTotal() {
                return this.selectedBills.reduce((sum, bill) => sum + parseFloat(bill.amount), 0);
            },

            openPaymentModal() {
                this.showPaymentModal = true;
            },

            async submitPayment() {
                if (!this.paymentMethod || !this.proofImage) { alert('Please fill in all required fields'); return; }
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
                    const response = await fetch('{{ route("administrator.payments.submit-manual") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
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
            },

            // Existing state
            showEditModal: false,
            showArchiveModal: false,
            showArchiveConfirm: false,
            showReceiptModal: false,
            editContributionId: null,
            editStatus: 'pending',
            archiveContributionId: null,
            archiveConfirmText: '',
            receiptData: { txn: '', date: '', method: '', name: '', phone: '', address: '', project: '', amount: 0 },

            // Filter states
            searchQuery: '{{ request('search') }}',
            statusFilter: '{{ request('status') }}',
            dateRangeFilter: '{{ request('date_range') }}',
            schoolYearFilter: '{{ request('school_year') }}',

            openEditModal(id, status) {
                this.editContributionId = id;
                this.editStatus = status;
                this.showEditModal = true;
            },
            openArchiveModal(id) {
                this.archiveContributionId = id;
                this.archiveConfirmText = '';
                this.showArchiveConfirm = false;
                this.showArchiveModal = true;
            },
            closeArchiveModal() {
                this.showArchiveModal = false;
                this.showArchiveConfirm = false;
                this.archiveConfirmText = '';
            },
            confirmArchive() {
                if (this.archiveConfirmText === 'Archive') {
                    alert('Payment archived successfully!');
                    this.closeArchiveModal();
                }
            },
            openReceiptModal(data) {
                this.receiptData = data;
                this.showReceiptModal = true;
            },
            formatCurrency(amount) {
                return new Intl.NumberFormat('en-PH', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
            },
            downloadReceipt() {
                const content = document.getElementById('receiptContent').innerHTML;
                const printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Receipt - ' + this.receiptData.txn + '</title><style>body{font-family:Arial,sans-serif;padding:20px;max-width:400px;margin:0 auto}.text-center{text-align:center}.text-xs{font-size:12px}.text-sm{font-size:14px}.text-lg{font-size:18px}.text-xl{font-size:20px}.font-bold{font-weight:bold}.font-semibold{font-weight:600}.font-medium{font-weight:500}.text-gray-500{color:#6b7280}.text-gray-600{color:#4b5563}.text-gray-900{color:#111827}.text-green-600{color:#059669}.border-dashed{border-style:dashed}.border-gray-300{border-color:#d1d5db}.border-t-2{border-top-width:2px}.my-4{margin:16px 0}.mb-4{margin-bottom:16px}.mt-2{margin-top:8px}.mt-6{margin-top:24px}.py-2{padding:8px 0}.space-y-2>*+*{margin-top:8px}.flex{display:flex}.justify-between{justify-content:space-between}.items-center{align-items:center}.tracking-widest{letter-spacing:.1em}.uppercase{text-transform:uppercase}</style></head><body>' + content + '</body></html>');
                printWindow.document.close();
                printWindow.print();
            },

            // Real-time filtering
            filterTable() {
                const rows = document.querySelectorAll('#paymentsTable tbody tr');
                const searchLower = this.searchQuery.toLowerCase().trim();
                let visibleCount = 0;

                rows.forEach(row => {
                    const parentName = row.getAttribute('data-parent')?.toLowerCase() || '';
                    const projectName = row.getAttribute('data-project')?.toLowerCase() || '';
                    const status = row.getAttribute('data-status') || '';
                    const date = row.getAttribute('data-date') || '';

                    let show = true;

                    // Search filter
                    if (searchLower && !parentName.includes(searchLower) && !projectName.includes(searchLower)) {
                        show = false;
                    }

                    // Status filter
                    if (this.statusFilter && status !== this.statusFilter) {
                        show = false;
                    }

                    // Date range filter
                    if (this.dateRangeFilter && date) {
                        const rowDate = new Date(date);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        switch(this.dateRangeFilter) {
                            case 'today':
                                const todayStr = today.toISOString().split('T')[0];
                                const rowDateStr = rowDate.toISOString().split('T')[0];
                                if (todayStr !== rowDateStr) show = false;
                                break;
                            case 'this_week':
                                const weekStart = new Date(today);
                                weekStart.setDate(today.getDate() - today.getDay());
                                const weekEnd = new Date(weekStart);
                                weekEnd.setDate(weekStart.getDate() + 6);
                                if (rowDate < weekStart || rowDate > weekEnd) show = false;
                                break;
                            case 'this_month':
                                if (rowDate.getMonth() !== today.getMonth() || rowDate.getFullYear() !== today.getFullYear()) show = false;
                                break;
                            case 'this_year':
                                if (rowDate.getFullYear() !== today.getFullYear()) show = false;
                                break;
                        }
                    }

                    // School year filter (simplified - you may need to adjust based on actual school year logic)
                    if (this.schoolYearFilter && date) {
                        const rowDate = new Date(date);
                        const year = rowDate.getFullYear();
                        const month = rowDate.getMonth();
                        // School year typically starts in June
                        const schoolYear = month >= 5 ? `${year}-${year + 1}` : `${year - 1}-${year}`;
                        if (schoolYear !== this.schoolYearFilter) show = false;
                    }

                    row.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                // Update visible count display
                this.updateResultsCount(visibleCount);
            },

            updateResultsCount(count) {
                const resultsText = document.getElementById('resultsCount');
                if (resultsText) {
                    const total = document.querySelectorAll('#paymentsTable tbody tr').length;
                    resultsText.innerHTML = `Showing <span class="font-medium text-green-600">1</span> to <span class="font-medium text-green-600">${count}</span> of <span class="font-medium text-green-600">${total}</span> results`;
                }
            },

            clearFilters() {
                this.searchQuery = '';
                this.statusFilter = '';
                this.dateRangeFilter = '';
                this.schoolYearFilter = '';
                this.filterTable();

                // Also clear the form inputs
                document.getElementById('searchInput').value = '';
                document.getElementById('statusFilter').value = '';
                document.getElementById('dateRangeFilter').value = '';
                document.getElementById('schoolYearFilter').value = '';
            }
        }
    }

    // Alpine store for admin manual payment bill toggling
    document.addEventListener('alpine:init', () => {
        Alpine.store('adminPaymentMgr', {
            toggleBill(checkbox) {
                const event = new CustomEvent('admin-toggle-bill', { detail: { checkbox } });
                window.dispatchEvent(event);
            }
        });
    });

    window.addEventListener('admin-toggle-bill', (event) => {
        const component = Alpine.$data(document.querySelector('[x-data="paymentsManager()"]'));
        if (component) {
            component.toggleBill(event.detail.checkbox);
        }
    });

    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        });
    }

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const total = document.querySelectorAll('.row-checkbox').length;
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.checked = total === checked;
                selectAll.indeterminate = checked > 0 && checked < total;
            }
        });
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
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Receipt Image</label>
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
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Update Status</label>

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
        form.action = `/administrator/payments/${contributionId}`;

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
