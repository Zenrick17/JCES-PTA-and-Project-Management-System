@extends('layouts.parent-sidebar')

@section('title', 'Payments')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Payment Selection Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
        <!-- Select All Button -->
        <div class="flex justify-end mb-6">
            @if(count($paymentItems) > 0)
            <button type="button" id="selectAllBtn" onclick="toggleSelectAll()"
                    class="px-5 py-2 bg-white border-2 border-green-500 hover:bg-green-50 text-green-600 text-sm font-semibold rounded-lg transition-all">
                Select All
            </button>
            @endif
        </div>

        <!-- Inner Content Box -->
        <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
            <!-- Amount Header -->
            <div class="flex justify-end px-6 py-3 border-b border-gray-200">
                <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</span>
            </div>

            <!-- Payment Items -->
            <div class="divide-y divide-gray-200 bg-white">
                @forelse($paymentItems as $item)
                    <label class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 cursor-pointer transition-colors">
                        <div class="flex items-center gap-4">
                            <input type="checkbox"
                                   name="payment[]"
                                   value="{{ $item['amount'] }}"
                                   data-project-id="{{ $item['project']->projectID }}"
                                   data-project-name="{{ $item['project']->project_name }}"
                                   class="payment-checkbox w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 cursor-pointer"
                                   onchange="updateTotal()"
                                   @if($item['is_pending']) disabled @endif>
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-900">{{ $item['project']->project_name }}</span>
                                @if($item['is_pending'])
                                    <span class="text-xs text-yellow-600">Payment pending verification</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900">₱{{ number_format($item['amount'], 2) }}</span>
                            @if($item['is_pending'])
                                <span class="block text-xs text-yellow-600">Pending: ₱{{ number_format($item['pending_amount'], 2) }}</span>
                            @endif
                        </div>
                    </label>
                @empty
                    <div class="px-6 py-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-600 font-medium">All payments are up to date!</p>
                        <p class="text-sm text-gray-500 mt-1">You have no pending payments at this time.</p>
                    </div>
                @endforelse
            </div>

            <!-- Empty space and Total -->
            <div class="bg-gray-50 px-6 py-8">
                <div class="flex justify-end">
                    <div class="text-base font-semibold text-gray-900">
                        Total: <span id="totalAmount" class="text-gray-900">₱0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        @if(count($paymentItems) > 0)
        <div class="flex justify-end gap-4 mt-6">
            <button type="button" onclick="exportPayments()"
                    class="px-6 py-3 bg-white border-2 border-gray-300 hover:border-gray-400 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export
            </button>

            <button type="button" onclick="processPayment()" id="payBtn"
                    class="px-8 py-3 bg-green-500 hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-all shadow-md hover:shadow-lg flex items-center gap-2"
                    disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Pay
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closePaymentModal()"></div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-4xl relative z-10" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.15);">
            <!-- Close Button -->
            <button onclick="closePaymentModal()" class="absolute top-4 left-4 w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition-all shadow-md hover:shadow-lg z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </button>

            <div class="p-8">
                <!-- Header -->
                <h2 class="text-xl font-bold text-gray-900 mb-6">Contact info</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Contact Info & Payment Mode -->
                    <div class="space-y-6">
                        <!-- Contact Info Box -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 shadow-md">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">NAME:</label>
                                <input type="text" id="payerName" value="{{ Auth::user()->first_name ?? '' }} {{ Auth::user()->last_name ?? '' }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>

                            <!-- Contact -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">CONTACT:</label>
                                <input type="text" id="payerContact" value="{{ Auth::user()->phone ?? '' }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">ADDRESS:</label>
                                <input type="text" id="payerAddress" value="{{ Auth::user()->address ?? '' }}"
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <!-- Mode of Payment -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mode of Payment:</h3>
                            <div class="flex gap-4">
                                <!-- GCash -->
                                <div id="gcashOption" onclick="selectPaymentMode('gcash')"
                                     class="cursor-pointer flex-1 px-5 py-4 bg-blue-50 border-2 border-blue-500 rounded-xl shadow-lg scale-105 transition-all duration-200 flex items-center justify-center gap-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">G</span>
                                    </div>
                                    <span class="text-blue-600 font-bold text-lg">GCash</span>
                                </div>

                                <!-- Maya -->
                                <div id="mayaOption" onclick="selectPaymentMode('maya')"
                                     class="cursor-pointer flex-1 px-5 py-4 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-3">
                                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">M</span>
                                    </div>
                                    <span class="text-green-500 font-bold text-lg">maya</span>
                                </div>
                            </div>
                            <input type="hidden" id="selectedPaymentMode" value="gcash">
                        </div>
                    </div>

                    <!-- Right Column - Receipt -->
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-md">
                        <!-- Receipt Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 text-center">
                            <h3 class="text-2xl font-bold text-gray-900 tracking-wide">RECEIPT</h3>
                        </div>

                        <!-- Receipt Items -->
                        <div id="receiptItems" class="p-6 space-y-3 min-h-[200px]">
                            <!-- Items will be populated by JavaScript -->
                        </div>

                        <!-- Receipt Footer -->
                        <div class="px-6 py-5 border-t border-gray-200 bg-white">
                            <div class="flex justify-between items-center mb-5">
                                <span class="text-lg font-semibold text-gray-700">Total</span>
                                <span id="receiptTotal" class="text-2xl font-bold text-gray-900">₱0</span>
                            </div>

                            <!-- Pay Button -->
                            <button onclick="confirmPayment()"
                                    class="w-full px-6 py-4 bg-green-500 hover:bg-green-600 text-white text-base font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Pay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Image Upload Modal -->
<div id="receiptUploadModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeReceiptUploadModal()"></div>

    <!-- Modal Content -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md relative z-10" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.15);">
            <!-- Close Button -->
            <button onclick="closeReceiptUploadModal()" class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full flex items-center justify-center transition-all z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="p-8">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Upload Payment Receipt</h2>
                    <p class="text-sm text-gray-600">Please upload a screenshot or photo of your payment receipt from <span id="paymentModeLabel" class="font-semibold text-green-600">GCash</span></p>
                </div>

                <!-- Upload Area -->
                <form id="receiptUploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_method" id="formPaymentMethod" value="gcash">
                    <input type="hidden" name="name" id="formPayerName">
                    <input type="hidden" name="contact" id="formPayerContact">
                    <input type="hidden" name="address" id="formPayerAddress">
                    <div id="projectInputs"></div>

                    <div class="mb-6">
                        <label for="receiptImageInput" class="block cursor-pointer">
                            <div id="uploadArea" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-500 hover:bg-green-50 transition-all">
                                <div id="uploadPlaceholder">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Click to upload receipt image</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                </div>
                                <div id="uploadPreview" class="hidden">
                                    <img id="previewImage" src="" alt="Receipt Preview" class="max-h-48 mx-auto rounded-lg mb-2">
                                    <p id="fileName" class="text-sm text-gray-600"></p>
                                </div>
                            </div>
                        </label>
                        <input type="file" id="receiptImageInput" name="receipt_image" accept="image/*" class="hidden" onchange="previewReceiptImage(this)">
                    </div>

                    <!-- Submit Button -->
                    <button type="button" onclick="submitPaymentWithReceipt()" id="submitPaymentBtn"
                            class="w-full px-6 py-4 bg-green-500 hover:bg-green-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-base font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-3"
                            disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="submitBtnText">Submit Payment</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function updateTotal() {
        const checkboxes = document.querySelectorAll('.payment-checkbox:checked:not(:disabled)');
        let total = 0;

        checkboxes.forEach(checkbox => {
            total += parseFloat(checkbox.value);
        });

        document.getElementById('totalAmount').textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Update Select All button text
        const allCheckboxes = document.querySelectorAll('.payment-checkbox:not(:disabled)');
        const selectAllBtn = document.getElementById('selectAllBtn');
        if (selectAllBtn) {
            if (checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0) {
                selectAllBtn.textContent = 'Deselect All';
            } else {
                selectAllBtn.textContent = 'Select All';
            }
        }

        // Enable/disable Pay button
        const payBtn = document.getElementById('payBtn');
        if (payBtn) {
            payBtn.disabled = checkboxes.length === 0;
        }
    }

    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.payment-checkbox:not(:disabled)');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });

        updateTotal();
    }

    function exportPayments() {
        const checkboxes = document.querySelectorAll('.payment-checkbox:checked:not(:disabled)');

        if (checkboxes.length === 0) {
            alert('Please select at least one payment item to export.');
            return;
        }

        // Gather payment data
        const userName = "{{ Auth::user()->first_name ?? '' }} {{ Auth::user()->last_name ?? '' }}".trim();
        const userContact = "{{ Auth::user()->phone ?? '' }}";
        const userAddress = "{{ Auth::user()->address ?? '' }}";
        const exportDate = new Date().toLocaleString('en-PH', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        let items = [];
        let total = 0;

        checkboxes.forEach(checkbox => {
            const projectName = checkbox.getAttribute('data-project-name');
            const amount = parseFloat(checkbox.value);
            items.push({ name: projectName, amount: amount });
            total += amount;
        });

        // Create CSV content
        let csvContent = "JCES PTA - Pending Payments\n";
        csvContent += "Generated: " + exportDate + "\n\n";
        csvContent += "Payer Information\n";
        csvContent += "Name," + userName + "\n";
        csvContent += "Contact," + userContact + "\n";
        csvContent += "Address," + userAddress + "\n\n";
        csvContent += "Payment Items\n";
        csvContent += "Description,Amount (PHP)\n";

        items.forEach(item => {
            csvContent += item.name + "," + item.amount.toFixed(2) + "\n";
        });

        csvContent += "\nTotal," + total.toFixed(2) + "\n";

        // Create and download file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'JCES_PTA_Pending_Payments_' + new Date().toISOString().slice(0,10) + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function processPayment() {
        const checkboxes = document.querySelectorAll('.payment-checkbox:checked:not(:disabled)');

        if (checkboxes.length === 0) {
            alert('Please select at least one payment item to proceed.');
            return;
        }

        // Populate receipt items
        const receiptItemsContainer = document.getElementById('receiptItems');
        receiptItemsContainer.innerHTML = '';

        checkboxes.forEach(checkbox => {
            const projectName = checkbox.getAttribute('data-project-name');
            const amount = parseFloat(checkbox.value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex justify-between items-center py-2 border-b border-gray-100';
            itemDiv.innerHTML = `
                <span class="text-sm text-gray-700">${projectName}</span>
                <span class="text-sm font-semibold text-gray-900">₱${amount}</span>
            `;
            receiptItemsContainer.appendChild(itemDiv);
        });

        // Update receipt total
        const total = document.getElementById('totalAmount').textContent;
        document.getElementById('receiptTotal').textContent = total;

        // Reset payment mode to GCash (default)
        selectPaymentMode('gcash');

        // Show modal
        document.getElementById('paymentModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    let selectedPaymentMode = 'gcash';

    function selectPaymentMode(mode) {
        selectedPaymentMode = mode;
        document.getElementById('selectedPaymentMode').value = mode;

        const gcashOption = document.getElementById('gcashOption');
        const mayaOption = document.getElementById('mayaOption');

        if (mode === 'gcash') {
            gcashOption.className = 'cursor-pointer flex-1 px-5 py-4 bg-blue-50 border-2 border-blue-500 rounded-xl shadow-lg scale-105 transition-all duration-200 flex items-center justify-center gap-3';
            mayaOption.className = 'cursor-pointer flex-1 px-5 py-4 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-3';
        } else {
            gcashOption.className = 'cursor-pointer flex-1 px-5 py-4 bg-white border-2 border-gray-200 rounded-xl shadow-md transition-all duration-200 flex items-center justify-center gap-3';
            mayaOption.className = 'cursor-pointer flex-1 px-5 py-4 bg-green-50 border-2 border-green-500 rounded-xl shadow-lg scale-105 transition-all duration-200 flex items-center justify-center gap-3';
        }
    }

    function confirmPayment() {
        const paymentMode = selectedPaymentMode;
        const name = document.getElementById('payerName').value;
        const contact = document.getElementById('payerContact').value;
        const address = document.getElementById('payerAddress').value;
        const total = document.getElementById('receiptTotal').textContent;

        if (!name || !contact || !address) {
            alert('Please fill in all contact information.');
            return;
        }

        // Prepare form data for receipt upload modal
        document.getElementById('formPaymentMethod').value = paymentMode;
        document.getElementById('formPayerName').value = name;
        document.getElementById('formPayerContact').value = contact;
        document.getElementById('formPayerAddress').value = address;
        document.getElementById('paymentModeLabel').textContent = paymentMode.toUpperCase();

        // Add project IDs and amounts to form
        const projectInputsContainer = document.getElementById('projectInputs');
        projectInputsContainer.innerHTML = '';

        const checkboxes = document.querySelectorAll('.payment-checkbox:checked:not(:disabled)');
        checkboxes.forEach((checkbox, index) => {
            const projectId = checkbox.getAttribute('data-project-id');
            const amount = checkbox.value;

            projectInputsContainer.innerHTML += `
                <input type="hidden" name="project_ids[]" value="${projectId}">
                <input type="hidden" name="amounts[]" value="${amount}">
            `;
        });

        // Reset upload area
        document.getElementById('uploadPlaceholder').classList.remove('hidden');
        document.getElementById('uploadPreview').classList.add('hidden');
        document.getElementById('receiptImageInput').value = '';
        document.getElementById('submitPaymentBtn').disabled = true;

        // Show receipt upload modal
        document.getElementById('receiptUploadModal').classList.remove('hidden');
    }

    function closeReceiptUploadModal() {
        document.getElementById('receiptUploadModal').classList.add('hidden');
    }

    function previewReceiptImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('uploadPreview').classList.remove('hidden');
                document.getElementById('submitPaymentBtn').disabled = false;
            };

            reader.readAsDataURL(file);
        }
    }

    function submitPaymentWithReceipt() {
        const form = document.getElementById('receiptUploadForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitPaymentBtn');
        const submitBtnText = document.getElementById('submitBtnText');

        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Submitting...';

        fetch('{{ route("parent.payments.submit") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close receipt upload modal
                closeReceiptUploadModal();

                // Show success modal
                const total = document.getElementById('receiptTotal').textContent;
                showSuccessModal(total, selectedPaymentMode);
            } else {
                alert(data.message || 'Failed to submit payment. Please try again.');
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Submit Payment';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtnText.textContent = 'Submit Payment';
        });
    }

    // Store receipt data for later use
    let currentReceiptData = null;

    function showSuccessModal(total, paymentMode) {
        // Store receipt data
        const receiptItems = document.getElementById('receiptItems').innerHTML;
        const name = document.getElementById('payerName').value;
        const contact = document.getElementById('payerContact').value;
        const address = document.getElementById('payerAddress').value;
        const transactionId = 'TXN' + Date.now().toString().slice(-10);
        const dateTime = new Date().toLocaleString('en-PH', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        currentReceiptData = {
            transactionId,
            dateTime,
            name,
            contact,
            address,
            total,
            paymentMode,
            receiptItems
        };

        // Remove existing success modal if any
        const existingModal = document.getElementById('successModal');
        if (existingModal) existingModal.remove();

        // Create success modal
        const successModal = document.createElement('div');
        successModal.id = 'successModal';
        successModal.className = 'fixed inset-0 flex items-center justify-center p-4';
        successModal.style.zIndex = '9999';
        successModal.innerHTML = `
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl p-8 max-w-md w-full relative text-center" style="z-index: 10000; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.15);">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                <p class="text-gray-600 mb-4">Your payment of <span class="font-semibold text-green-600">${total}</span> via ${paymentMode.toUpperCase()} has been processed successfully.</p>
                <p class="text-sm text-gray-500 mb-6">A confirmation receipt has been sent to your registered contact.</p>
                <button onclick="showReceiptModal()" class="px-8 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-all shadow-md hover:shadow-lg">
                    Done
                </button>
            </div>
        `;
        document.body.appendChild(successModal);
    }

    function showReceiptModal() {
        // Remove success modal
        const successModal = document.getElementById('successModal');
        if (successModal) successModal.remove();

        // Create receipt modal - typical receipt paper size (narrow)
        const receiptModal = document.createElement('div');
        receiptModal.id = 'receiptModal';
        receiptModal.className = 'fixed inset-0 flex items-center justify-center p-4 overflow-y-auto';
        receiptModal.style.zIndex = '9999';
        receiptModal.innerHTML = `
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeReceiptModal()"></div>
            <div class="bg-white relative my-8" style="z-index: 10000; width: 280px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 40px rgba(0, 0, 0, 0.15);">
                <!-- Receipt Paper -->
                <div id="receiptContent" class="px-4 py-5">
                    <!-- Header -->
                    <div class="text-center mb-3 pb-3 border-b border-dashed border-gray-400">
                        <div class="text-[10px] text-gray-500 mb-1">*** OFFICIAL RECEIPT ***</div>
                        <h2 class="text-sm font-bold text-gray-900">JCES PTA</h2>
                        <p class="text-[10px] text-gray-500">J. Cruz Sr. Elementary School</p>
                        <p class="text-[10px] text-gray-500">Parent-Teacher Association</p>
                    </div>

                    <!-- Transaction Info -->
                    <div class="mb-3 pb-3 border-b border-dashed border-gray-300 text-[10px] space-y-0.5">
                        <div class="flex justify-between">
                            <span class="text-gray-500">TXN #:</span>
                            <span class="font-mono font-medium">${currentReceiptData.transactionId}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Date:</span>
                            <span class="font-medium">${currentReceiptData.dateTime}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Payment:</span>
                            <span class="font-medium">${currentReceiptData.paymentMode.toUpperCase()}</span>
                        </div>
                    </div>

                    <!-- Payer Info -->
                    <div class="mb-3 pb-3 border-b border-dashed border-gray-300 text-[10px]">
                        <div class="text-gray-500 mb-0.5">PAID BY:</div>
                        <div class="font-medium text-gray-900">${currentReceiptData.name}</div>
                        <div class="text-gray-600">${currentReceiptData.contact}</div>
                        <div class="text-gray-600">${currentReceiptData.address}</div>
                    </div>

                    <!-- Items -->
                    <div class="mb-3 text-[10px]">
                        <div class="text-gray-500 mb-1">ITEMS:</div>
                        <div class="space-y-0.5">
                            ${currentReceiptData.receiptItems}
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="py-2 border-t border-b border-double border-gray-400 mb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-900">TOTAL</span>
                            <span class="text-sm font-bold text-gray-900">${currentReceiptData.total}</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center text-[10px] text-gray-500 space-y-0.5">
                        <p>Thank you for your contribution!</p>
                        <p>- - - - - - - - - - - - - - - - -</p>
                        <p class="font-medium">This serves as your Official Receipt</p>
                        <p>Please keep for your records</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-3 pb-3 bg-gray-50 flex flex-col gap-1.5">
                    <button onclick="exportReceipt()" class="w-full px-3 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-md transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Receipt
                    </button>
                    <button onclick="closeReceiptModal()" class="w-full px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium rounded-md transition-all">
                        Close
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(receiptModal);
    }

    function closeReceiptModal() {
        const receiptModal = document.getElementById('receiptModal');
        if (receiptModal) receiptModal.remove();

        closePaymentModal();
        closeReceiptUploadModal();

        // Refresh the page to show updated payment status
        window.location.reload();
    }

    function exportReceipt() {
        // Create a printable receipt version - narrow paper style
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Receipt - ${currentReceiptData.transactionId}</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body {
                        font-family: 'Courier New', monospace;
                        padding: 20px;
                        max-width: 280px;
                        margin: 0 auto;
                        font-size: 12px;
                        line-height: 1.4;
                    }
                    .header { text-align: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #000; }
                    .header .title { font-size: 10px; margin-bottom: 5px; }
                    .header h2 { font-size: 16px; font-weight: bold; margin-bottom: 3px; }
                    .header p { font-size: 10px; }
                    .section { margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px dashed #000; }
                    .row { display: flex; justify-content: space-between; margin-bottom: 3px; }
                    .row .label { color: #555; }
                    .row .value { font-weight: 500; text-align: right; }
                    .payer-section .label { margin-bottom: 5px; }
                    .payer-section .info { margin-left: 0; }
                    .items-section .label { margin-bottom: 8px; }
                    .item-row { display: flex; justify-content: space-between; padding: 3px 0; }
                    .total-section {
                        padding: 10px 0;
                        border-top: 2px double #000;
                        border-bottom: 2px double #000;
                        margin: 10px 0;
                    }
                    .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; }
                    .footer { text-align: center; font-size: 10px; margin-top: 15px; }
                    .footer p { margin: 3px 0; }
                    .divider { text-align: center; margin: 8px 0; }
                    @media print {
                        body { padding: 10px; }
                        @page { margin: 0; size: 80mm auto; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">*** OFFICIAL RECEIPT ***</div>
                    <h2>JCES PTA</h2>
                    <p>J. Cruz Sr. Elementary School</p>
                    <p>Parent-Teacher Association</p>
                </div>

                <div class="section">
                    <div class="row"><span class="label">TXN #:</span><span class="value">${currentReceiptData.transactionId}</span></div>
                    <div class="row"><span class="label">Date:</span><span class="value">${currentReceiptData.dateTime}</span></div>
                    <div class="row"><span class="label">Payment:</span><span class="value">${currentReceiptData.paymentMode.toUpperCase()}</span></div>
                </div>

                <div class="section payer-section">
                    <div class="label">PAID BY:</div>
                    <div class="info">
                        <div style="font-weight: 500;">${currentReceiptData.name}</div>
                        <div>${currentReceiptData.contact}</div>
                        <div>${currentReceiptData.address}</div>
                    </div>
                </div>

                <div class="section items-section">
                    <div class="label">ITEMS:</div>
                    ${currentReceiptData.receiptItems}
                </div>

                <div class="total-section">
                    <div class="total-row">
                        <span>TOTAL</span>
                        <span>${currentReceiptData.total}</span>
                    </div>
                </div>

                <div class="footer">
                    <p>Thank you for your contribution!</p>
                    <p class="divider">--------------------------------</p>
                    <p style="font-weight: 500;">This serves as your Official Receipt</p>
                    <p>Please keep for your records</p>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    function closeSuccessModal() {
        const successModal = document.getElementById('successModal');
        if (successModal) {
            successModal.remove();
        }
        closePaymentModal();

        // Reset checkboxes
        document.querySelectorAll('.payment-checkbox').forEach(cb => cb.checked = false);
        updateTotal();
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePaymentModal();
        }
    });
</script>
@endsection
