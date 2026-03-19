@extends('layouts.pr-sidebar')

@section('title', 'Payments & Contributions')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h1 class="text-lg font-semibold text-gray-900">Payment</h1>
            <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Filter
                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        @php
                            $status = $payment->payment_status;
                            $statusLabel = $status === 'completed' ? 'Paid' : ($status === 'pending' ? 'Pending' : 'Unpaid');
                        @endphp
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                {{ $payment->parent ? $payment->parent->first_name . ' ' . $payment->parent->last_name : 'Unknown Parent' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-900">₱{{ number_format($payment->contribution_amount, 2) }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $payment->project?->project_name ?? 'Unknown Project' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ optional($payment->contribution_date)->format('m-d-Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $statusLabel }}</td>
                            <td class="px-6 py-3 text-right">
                                <button type="button" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $payments->links() }}
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contribution History</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Parent</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Project</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($recentContributions as $contribution)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ optional($contribution->contribution_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                {{ $contribution->parent ? $contribution->parent->first_name . ' ' . $contribution->parent->last_name : 'Unknown Parent' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ $contribution->project?->project_name ?? 'Unknown Project' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900 text-right">₱{{ number_format($contribution->contribution_amount, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ ucfirst($contribution->payment_status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-sm text-gray-500 text-center">No contributions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
