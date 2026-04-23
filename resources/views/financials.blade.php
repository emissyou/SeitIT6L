@extends('layouts.app')

@section('title', 'Financials')
@section('subtitle', 'Payment totals and outstanding balances')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">Outstanding Balance</div>
        <div class="mt-4 text-3xl font-semibold text-slate-900">₱{{ number_format($outstanding, 2) }}</div>
    </div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">Total Payments</div>
        <div class="mt-4 text-3xl font-semibold text-slate-900">₱{{ number_format($totalPayments, 2) }}</div>
    </div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">Recent Payments</div>
        <div class="mt-4 text-sm text-slate-600">Showing the latest payments recorded in the system.</div>
    </div>
</div>

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-semibold text-slate-900">Recent Payments</h2>
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-left text-sm text-slate-600">
            <thead class="border-b border-slate-200 text-slate-500">
                <tr>
                    <th class="px-3 py-3">Date</th>
                    <th class="px-3 py-3">Amount</th>
                    <th class="px-3 py-3">Customer</th>
                    <th class="px-3 py-3">Credit Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($recentPayments as $payment)
                    <tr>
                        <td class="px-3 py-4">{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td class="px-3 py-4">₱{{ number_format($payment->amount_paid, 2) }}</td>
                        <td class="px-3 py-4">{{ $payment->credit->customer->first_name ?? 'N/A' }} {{ $payment->credit->customer->last_name ?? '' }}</td>
                        <td class="px-3 py-4">₱{{ number_format($payment->credit->balance ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-4 text-slate-500">No payments have been recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
