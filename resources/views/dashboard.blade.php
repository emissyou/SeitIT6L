@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of Seal fuel station operations')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">Current Fuel Stock</div>
        <div class="mt-6 space-y-4">
            @forelse ($stockSummary as $stock)
                <div class="rounded-3xl border border-slate-100 p-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium text-slate-600">{{ $stock->fuel_name }}</div>
                            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($stock->current_stock, 3) }} L</div>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ number_format($stock->percent_full, 1) }}%</span>
                    </div>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-emerald-500" style="width: {{ min(100, max(0, $stock->percent_full)) }}%"></div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-slate-100 p-4 text-sm text-slate-500">No stock data available yet.</div>
            @endforelse
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">Customers with Credit</div>
        <div class="mt-6">
            <div class="text-3xl font-semibold text-slate-900">₱{{ number_format($outstandingCredit, 2) }}</div>
            <div class="mt-2 text-sm text-slate-500">{{ $customerCount }} customers with outstanding balance</div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="text-sm font-semibold text-slate-500">Last Shift Summary</div>
        <div class="mt-6 space-y-3 text-sm text-slate-700">
            @if ($lastShift)
                <div><span class="font-semibold">Date:</span> {{ \Illuminate\Support\Carbon::parse($lastShift->sales_date)->format('F j, Y') }}</div>
                <div><span class="font-semibold">Gross Sales:</span> ₱{{ number_format($lastShift->gross_sales, 2) }}</div>
                <div><span class="font-semibold">Net Sales:</span> ₱{{ number_format($lastShift->net_sales, 2) }}</div>
                <div><span class="font-semibold">Status:</span> {{ ucfirst($lastShift->status) }}</div>
            @else
                <div class="text-slate-500">No closed shifts yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection
