@extends('layouts.app')

@section('title', 'Wetstock')
@section('subtitle', 'Monitor fuel inventory and deliveries')

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-3">
        <div class="text-sm font-semibold text-slate-500">Current Stock Levels</div>
        <div class="mt-6 grid gap-4 lg:grid-cols-3">
            @forelse ($stockSummary as $stock)
                <div class="rounded-3xl border border-slate-100 p-4">
                    <div class="text-sm font-medium text-slate-700">{{ $stock->fuel_name }}</div>
                    <div class="mt-4 text-3xl font-semibold text-slate-900">{{ number_format($stock->current_stock, 3) }} / {{ number_format($stock->capacity, 3) }} L</div>
                    <div class="mt-2 text-sm text-slate-500">{{ number_format($stock->percent_full, 1) }}% capacity</div>
                </div>
            @empty
                <div class="rounded-3xl border border-slate-100 p-4 text-sm text-slate-500">No inventory data available.</div>
            @endforelse
        </div>
    </div>

    <div class="lg:col-span-3 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Delivery History</h2>
                <p class="mt-1 text-sm text-slate-500">Recent fuel deliveries and suppliers.</p>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-600">
                <thead class="border-b border-slate-200 text-slate-500">
                    <tr>
                        <th class="px-3 py-3">Date</th>
                        <th class="px-3 py-3">Supplier</th>
                        <th class="px-3 py-3">Driver</th>
                        <th class="px-3 py-3">Plate</th>
                        <th class="px-3 py-3">Total Cost</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($deliveries as $delivery)
                        <tr>
                            <td class="px-3 py-4">{{ \Illuminate\Support\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</td>
                            <td class="px-3 py-4">{{ $delivery->supplier->company_name ?? '—' }}</td>
                            <td class="px-3 py-4">{{ $delivery->driver }}</td>
                            <td class="px-3 py-4">{{ $delivery->plate_number }}</td>
                            <td class="px-3 py-4">₱{{ number_format($delivery->total_cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-slate-500">No deliveries recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
