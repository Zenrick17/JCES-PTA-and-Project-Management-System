@extends('layouts.ad-sidebar')

@section('title', 'Financial Summary')

@section('content')
<div class="space-y-6" id="printable-content">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Financial Summary</h1>
                <p class="text-gray-600 mt-1">Review transactions by date range and payment method.</p>
            </div>
            <div class="flex gap-2 print:hidden">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Export
                        <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                        <a href="{{ route('administrator.reports.financial-export', request()->query()) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Export as CSV
                        </a>
                        <button onclick="window.print()" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
                            </svg>
                            Print / Save as PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4 print:hidden">
            <div>
                <label class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Methods</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method }}" {{ $paymentMethod === $method ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $method)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Transactions</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalTransactions) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Amount</p>
            <p class="text-3xl font-bold text-green-600">₱{{ number_format($totalAmount, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Totals by Payment Method</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($methodTotals as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $row->payment_method)) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($row->total, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($row->count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-sm text-gray-500 text-center">No payment data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Totals by Project</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($projectTotals as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $row->project?->project_name ?? 'Unknown Project' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($row->total, 2) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($row->count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-sm text-gray-500 text-center">No project totals available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h2>
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
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ optional($transaction->transaction_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                {{ $transaction->parent ? $transaction->parent->first_name . ' ' . $transaction->parent->last_name : 'Unknown Parent' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $transaction->project?->project_name ?? 'Unknown Project' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $transaction->receipt_number }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-sm text-gray-500 text-center">No transactions found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 print:hidden">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-content, #printable-content * {
            visibility: visible;
        }
        #printable-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:hidden {
            display: none !important;
        }
        .shadow {
            box-shadow: none !important;
        }
    }
</style>
@endpush
@endsection
