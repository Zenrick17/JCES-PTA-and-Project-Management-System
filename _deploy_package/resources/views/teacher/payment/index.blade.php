@extends('layouts.te-sidebar')

@section('title', 'Payments')

@section('content')
<div class="space-y-6">
    <!-- Manual Payment Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Process Manual Payment</h2>

        <!-- Parent Selection with Search -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Select Parent</label>
            <div class="relative">
                <input
                    type="text"
                    id="parentSearch"
                    oninput="filterParents(this.value)"
                    onfocus="showDropdown()"
                    placeholder="Search parent by name or email..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 pr-10"
                    autocomplete="off"
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div id="parentDropdown" class="hidden absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                    <!-- Dropdown items will be populated by JavaScript -->
                </div>
            </div>
            <input type="hidden" id="selectedParentId" />
        </div>

        <!-- Parent Bills Container (will be populated via AJAX) -->
        <div id="parentBillsContainer" class="hidden">
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Unpaid Bills for <span id="selectedParentName" class="text-green-600"></span></h4>
                <div id="parentBillsList" class="space-y-2">
                    <!-- Bills will be loaded here -->
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Total Amount: <span id="manualTotal" class="text-xl font-bold text-green-600">₱0.00</span>
                </div>
                <button onclick="processManualPayment()" id="processManualBtn" disabled class="px-6 py-2.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-all shadow-md hover:shadow-lg">
                    Process Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Payment History</h2>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search parent or project..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    />
                </div>
                <div>
                    <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-28">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-40">Parent</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-48">Project</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 w-28">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-24">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-40">Processed By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-24">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 w-24">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($contributions as $contribution)
                        @php
                            $status = $contribution->payment_status;
                            $statusLabel = $status === 'completed' ? 'Paid' : ($status === 'pending' ? 'Pending' : 'Unpaid');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ optional($contribution->contribution_date)->format('m-d-Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $contribution->parent ? $contribution->parent->first_name . ' ' . $contribution->parent->last_name : 'Unknown Parent' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $contribution->project?->project_name ?? 'Unknown Project' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">₱{{ number_format($contribution->contribution_amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ strtoupper($contribution->payment_method) }}</td>
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
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $status === 'completed' ? 'bg-green-100 text-green-700' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-center">
                                @if($contribution->receipt_number)
                                    <button onclick="viewReceipt({{ $contribution->contributionID }})" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
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
</div>

<!-- Manual Payment Modal -->
<div id="manualPaymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeManualPaymentModal()"></div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl relative z-10" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.15);">
            <!-- Close Button -->
            <button onclick="closeManualPaymentModal()" class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition-all z-10">
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

                <!-- Payment Receipt -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Payment Summary</h3>
                    <div id="modalReceiptItems" class="space-y-2 mb-3">
                        <!-- Items will be loaded here -->
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Total</span>
                        <span id="modalTotal" class="text-lg font-bold text-gray-900">₱0.00</span>
                    </div>
                </div>

                <!-- Payment Form -->
                <form id="manualPaymentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="parent_id" id="formParentId">
                    <div id="modalProjectInputs"></div>

                    <div class="space-y-4 mb-6">
                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer" onclick="selectPaymentMode('cash')">
                                    <input type="radio" name="payment_method" value="cash" checked class="sr-only peer">
                                    <div id="cashOption" class="px-4 py-3 bg-gray-50 border-2 border-gray-500 rounded-xl shadow-lg scale-105 transition-all duration-200 flex items-center justify-center gap-2">
                                        <div class="w-7 h-7 bg-gray-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">Cash</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer" onclick="selectPaymentMode('gcash')">
                                    <input type="radio" name="payment_method" value="gcash" class="sr-only peer">
                                    <div id="gcashOption" class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2">
                                        <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold text-blue-700">GCash</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer" onclick="selectPaymentMode('maya')">
                                    <input type="radio" name="payment_method" value="maya" class="sr-only peer">
                                    <div id="mayaOption" class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2">
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
                            <label for="paymentNotes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea
                                id="paymentNotes"
                                name="notes"
                                rows="2"
                                placeholder="Additional payment details..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            ></textarea>
                        </div>

                        <!-- Proof of Payment Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Proof of Payment</label>
                            <label for="proofImageInput" class="block cursor-pointer">
                                <div id="proofUploadArea" class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-500 hover:bg-blue-50 transition-all">
                                    <div id="proofPlaceholder">
                                        <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-700 mb-1">Click to upload payment proof</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                    </div>
                                    <div id="proofPreview" class="hidden">
                                        <img id="proofPreviewImage" src="" alt="Proof Preview" class="max-h-32 mx-auto rounded-lg mb-2">
                                        <p id="proofFileName" class="text-sm text-gray-600"></p>
                                    </div>
                                </div>
                            </label>
                            <input type="file" id="proofImageInput" name="proof_image" accept="image/*" class="hidden" onchange="previewProofImage(this)">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" onclick="submitManualPayment()" id="submitManualBtn"
                            class="w-full px-6 py-4 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-base font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3"
                            disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="submitManualBtnText">Submit Payment</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div id="successToast" class="fixed top-4 right-4 z-[100] hidden">
    <div class="bg-white rounded-lg shadow-lg p-4 flex items-center gap-3 border-l-4 border-green-500">
        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-gray-900">Payment Processed!</p>
            <p class="text-sm text-gray-600">Payment has been recorded successfully.</p>
        </div>
    </div>
</div>

<script>
let selectedBills = [];
let selectedParentId = null;
let selectedParentName = '';

// Parent data from server
const allParents = {!! json_encode($parents) !!};

function showDropdown() {
    const dropdown = document.getElementById('parentDropdown');
    filterParents(document.getElementById('parentSearch').value);
    dropdown.classList.remove('hidden');
}

function hideDropdown() {
    setTimeout(() => {
        document.getElementById('parentDropdown').classList.add('hidden');
    }, 200);
}

function filterParents(search) {
    const dropdown = document.getElementById('parentDropdown');
    let filtered = allParents;

    if (search && search.trim() !== '') {
        const searchLower = search.toLowerCase();
        filtered = allParents.filter(parent => {
            const fullName = `${parent.last_name}, ${parent.first_name}`.toLowerCase();
            const email = (parent.email || '').toLowerCase();
            return fullName.includes(searchLower) || email.includes(searchLower);
        });
    }

    if (filtered.length === 0) {
        dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">No parents found</div>';
    } else {
        dropdown.innerHTML = filtered.map(parent => `
            <div onclick="selectParent(${parent.parentID}, '${escapeJs(parent.first_name)}', '${escapeJs(parent.last_name)}', '${escapeJs(parent.email)}')"
                 class="px-4 py-2 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                <span class="font-medium text-gray-900">${escapeHtml(parent.last_name)}, ${escapeHtml(parent.first_name)}</span>
                <span class="text-sm text-gray-500 ml-2">(${escapeHtml(parent.email || '')})</span>
            </div>
        `).join('');
    }
    dropdown.classList.remove('hidden');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function escapeJs(text) {
    if (!text) return '';
    return String(text).replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function selectParent(parentId, firstName, lastName, email) {
    document.getElementById('parentSearch').value = `${lastName}, ${firstName} (${email})`;
    document.getElementById('selectedParentId').value = parentId;
    document.getElementById('parentDropdown').classList.add('hidden');
    selectedParentId = parentId;
    selectedParentName = `${firstName} ${lastName}`;
    loadParentBills(parentId);
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const search = document.getElementById('parentSearch');
    const dropdown = document.getElementById('parentDropdown');
    if (search && dropdown && !search.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});

function loadParentBills(parentId) {
    if (!parentId) {
        document.getElementById('parentBillsContainer').classList.add('hidden');
        selectedParentId = null;
        selectedParentName = '';
        return;
    }

    // Show loading state
    const billsList = document.getElementById('parentBillsList');
    billsList.innerHTML = '<div class="text-center py-4"><svg class="animate-spin h-5 w-5 mx-auto text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';

    // Fetch parent's unpaid bills
    fetch(`/teacher/payments/parent-bills/${parentId}`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayParentBills(data.bills);
            document.getElementById('selectedParentName').textContent = selectedParentName;
            document.getElementById('parentBillsContainer').classList.remove('hidden');
        } else {
            billsList.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Failed to load bills.</p>';
            document.getElementById('parentBillsContainer').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        billsList.innerHTML = '<p class="text-sm text-red-500 text-center py-4">Error loading bills.</p>';
        document.getElementById('parentBillsContainer').classList.remove('hidden');
    });
}

function displayParentBills(bills) {
    const billsList = document.getElementById('parentBillsList');

    if (bills.length === 0) {
        billsList.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">No unpaid bills found for this parent.</p>';
        return;
    }

    billsList.innerHTML = bills.map(bill => `
        <label class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-green-300 cursor-pointer transition-all">
            <div class="flex items-center gap-3">
                <input
                    type="checkbox"
                    class="bill-checkbox w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                    data-project-id="${bill.projectID}"
                    data-project-name="${bill.project_name}"
                    data-amount="${bill.amount}"
                    onchange="updateManualTotal()"
                >
                <span class="text-sm text-gray-900">${bill.project_name}</span>
            </div>
            <span class="text-sm font-semibold text-gray-900">₱${parseFloat(bill.amount).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
        </label>
    `).join('');
}

function updateManualTotal() {
    const checkboxes = document.querySelectorAll('.bill-checkbox:checked');
    let total = 0;
    selectedBills = [];

    checkboxes.forEach(checkbox => {
        const amount = parseFloat(checkbox.getAttribute('data-amount'));
        total += amount;
        selectedBills.push({
            projectID: checkbox.getAttribute('data-project-id'),
            projectName: checkbox.getAttribute('data-project-name'),
            amount: amount
        });
    });

    document.getElementById('manualTotal').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('processManualBtn').disabled = selectedBills.length === 0;
}

function processManualPayment() {
    if (selectedBills.length === 0) {
        alert('Please select at least one bill to process.');
        return;
    }

    // Populate modal with selected bills
    const modalReceiptItems = document.getElementById('modalReceiptItems');
    modalReceiptItems.innerHTML = selectedBills.map(bill => `
        <div class="flex justify-between items-center py-2 border-b border-gray-100">
            <span class="text-sm text-gray-700">${bill.projectName}</span>
            <span class="text-sm font-semibold text-gray-900">₱${bill.amount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
        </div>
    `).join('');

    const total = selectedBills.reduce((sum, bill) => sum + bill.amount, 0);
    document.getElementById('modalTotal').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    // Set parent ID in form
    document.getElementById('formParentId').value = selectedParentId;

    // Add project IDs and amounts to form
    const projectInputsContainer = document.getElementById('modalProjectInputs');
    projectInputsContainer.innerHTML = selectedBills.map(bill => `
        <input type="hidden" name="project_ids[]" value="${bill.projectID}">
        <input type="hidden" name="amounts[]" value="${bill.amount}">
    `).join('');

    // Reset form
    document.getElementById('manualPaymentForm').reset();
    document.getElementById('proofPlaceholder').classList.remove('hidden');
    document.getElementById('proofPreview').classList.add('hidden');
    document.getElementById('proofImageInput').value = '';
    document.getElementById('submitManualBtn').disabled = true;

    // Show modal
    document.getElementById('manualPaymentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeManualPaymentModal() {
    document.getElementById('manualPaymentModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function previewProofImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('proofPreviewImage').src = e.target.result;
            document.getElementById('proofFileName').textContent = file.name;
            document.getElementById('proofPlaceholder').classList.add('hidden');
            document.getElementById('proofPreview').classList.remove('hidden');
            document.getElementById('submitManualBtn').disabled = false;
        };

        reader.readAsDataURL(file);
    }
}

function submitManualPayment() {
    const form = document.getElementById('manualPaymentForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitManualBtn');
    const submitBtnText = document.getElementById('submitManualBtnText');

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtnText.textContent = 'Processing...';

    fetch('{{ route("teacher.payments.submit-manual") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Server error');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            closeManualPaymentModal();

            // Show success toast
            const toast = document.getElementById('successToast');
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);

            // Reset selection and reload page
            selectedBills = [];
            selectedParentId = null;
            document.getElementById('parentSelect').value = '';
            document.getElementById('parentBillsContainer').classList.add('hidden');

            // Reload page to show new payment
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alert(data.message || 'Failed to process payment. Please try again.');
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Submit Payment';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
        submitBtn.disabled = false;
        submitBtnText.textContent = 'Submit Payment';
    });
}

function viewReceipt(contributionId) {
    window.open(`/teacher/payments/${contributionId}/receipt`, '_blank');
}

function selectPaymentMode(mode) {
    const cashOption = document.getElementById('cashOption');
    const gcashOption = document.getElementById('gcashOption');
    const mayaOption = document.getElementById('mayaOption');

    // Reset all to default style
    cashOption.className = 'px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2';
    gcashOption.className = 'px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2';
    mayaOption.className = 'px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-2';

    // Apply selected style
    if (mode === 'cash') {
        cashOption.className = 'px-4 py-3 bg-gray-50 border-2 border-gray-500 rounded-xl shadow-lg scale-105 transition-all duration-200 flex items-center justify-center gap-2';
    } else if (mode === 'gcash') {
        gcashOption.className = 'px-4 py-3 bg-blue-50 border-2 border-blue-500 rounded-xl shadow-lg scale-105 transition-all duration-200 flex items-center justify-center gap-2';
    } else {
        mayaOption.className = 'px-4 py-3 bg-green-50 border-2 border-green-500 rounded-xl shadow-lg scale-105 transition-all duration-200 flex items-center justify-center gap-2';
    }
}
</script>
@endsection
