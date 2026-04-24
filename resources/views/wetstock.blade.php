@extends('layouts.app')

@section('title', 'Wetstock')
@section('subtitle', 'Monitor fuel inventory and deliveries')

@section('content')
<div class="space-y-8">
    <!-- Separator line -->
    <div class="border-b border-slate-200"></div>

    <!-- Header Section -->
    <div class="flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Fuel Inventory</h2>
            <p class="mt-2 text-slate-600">Real-time monitoring of current fuel stock levels</p>
        </div>
        <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-red-600 bg-gradient-to-r from-red-600 to-red-700 px-6 py-2.5 font-semibold text-white shadow-md transition-all hover:from-red-700 hover:to-red-800 hover:shadow-lg active:scale-95" data-bs-toggle="modal" data-bs-target="#addDeliveryModal">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Delivery
        </button>
    </div>

    <!-- Current Stock Level Cards -->
    <div class="grid gap-4 lg:grid-cols-3">
        @forelse ($stockSummary as $stock)
            @php
                $tankLabel = ['A', 'B', 'C'][$loop->index] ?? 'A';
                $fuelLabel = strtolower(str_replace(' ', '', $stock->fuel_name));
                if ($fuelLabel === 'diesel') {
                    $tankColor = 'bg-amber-500/80';
                    $textClass = 'text-amber-950';
                    $borderColor = 'border-amber-300';
                } elseif ($fuelLabel === 'premium') {
                    $tankColor = 'bg-blue-600/80';
                    $textClass = 'text-blue-950';
                    $borderColor = 'border-blue-300';
                } else {
                    $tankColor = 'bg-slate-700/80';
                    $textClass = 'text-slate-950';
                    $borderColor = 'border-slate-300';
                }
                $percentFull = min($stock->percent_full, 100);
                $lastDelivery = isset($stock->last_delivery_date) ? \Illuminate\Support\Carbon::parse($stock->last_delivery_date)->format('Y-m-d') : 'N/A';
            @endphp
            <div class="overflow-hidden rounded-3xl border-2 {{ $borderColor }} bg-white shadow-lg transition-all hover:-translate-y-1 hover:shadow-xl">
                <div class="p-3">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.3em] text-slate-500">Tank {{ $tankLabel }}</p>
                            <h3 class="mt-1 text-base font-semibold {{ $textClass }}">{{ $stock->fuel_name }}</h3>
                        </div>
                    </div>

                    <div class="mt-3 relative h-28 w-full overflow-hidden rounded-3xl bg-slate-100 border border-slate-200">
                        <div class="absolute inset-x-0 bottom-0 rounded-b-3xl {{ $tankColor }} transition-all" style="height: {{ $percentFull }}%;"></div>
                        <div class="absolute inset-0 flex items-center justify-center text-sm font-semibold text-slate-950">{{ number_format($stock->percent_full, 0) }}%</div>
                    </div>

                    <div class="mt-3 rounded-3xl px-3 py-2 text-sm text-slate-600">
                        <div class="grid grid-cols-2">
                            <div>
                                <p class="text-[9px] uppercase tracking-[0.25em] text-slate-500">Current</p>
                                <p class=" font-semibold text-slate-900">{{ number_format($stock->current_stock, 0) }} L</p>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase tracking-[0.25em] text-slate-500">Capacity</p>
                                <p class=" font-semibold text-slate-900">{{ number_format($stock->capacity, 0) }} L</p>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase tracking-[0.25em] text-slate-500">Price</p>
                                <p class=" font-semibold text-slate-900">₱{{ number_format($stock->price_per_liter ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase tracking-[0.25em] text-slate-500">Last Delivery</p>
                                <p class=" font-semibold text-slate-900">{{ $lastDelivery }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-12 text-center">
                <p class="text-slate-600">No inventory data available yet.</p>
            </div>
        @endforelse
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-emerald-700 shadow-sm">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Delivery History Section -->
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-6 flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Delivery History</h2>
                <p class="mt-2 text-slate-600">Track all incoming fuel deliveries and costs</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Date</th>
                        <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Supplier</th>
                         <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Driver</th>
                        <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Plate</th>
                        <th class="px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-600">Fuel Type</th>
                        <th class="px-4 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-600">Quantity</th>
                        <th class="px-4 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-600">Unit Cost</th>
                        <th class="px-4 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-600">Total Cost</th>
                       
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($deliveries as $delivery)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="px-4 py-4 text-sm font-medium text-slate-900">{{ \Illuminate\Support\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">
                                <div>{{ $delivery->supplier->company_name ?? '—' }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $delivery->supplier->contact_name ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $delivery->driver }}</td>
                            <td class="px-4 py-4 text-sm text-slate-600">{{ $delivery->plate_number ?? '—' }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">
                                <span class="inline-block rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $delivery->details->first()?->fuel->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-medium text-slate-900">{{ number_format($delivery->details->first()?->quantity ?? 0, 2) }} L</td>
                            <td class="px-4 py-4 text-right text-sm text-slate-700">₱{{ number_format($delivery->details->first()?->unit_cost ?? 0, 2) }}</td>
                            <td class="px-4 py-4 text-right text-sm font-bold text-slate-900">₱{{ number_format($delivery->total_cost, 2) }}</td>
                            
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-500">No deliveries recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Delivery Modal -->
<div class="modal fade" id="addDeliveryModal" tabindex="-1" aria-labelledby="addDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl">
            <div class="modal-header border-b border-slate-200 bg-gradient-to-r from-red-50 to-orange-50 px-8 py-6">
                <div>
                    <h5 class="modal-title text-xl font-bold text-slate-900" id="addDeliveryModalLabel">Add Fuel Delivery</h5>
                    <p class="mt-1 text-sm text-slate-600">Record a new fuel delivery from supplier</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('wetstock.delivery.store') }}">
                @csrf
                <div class="modal-body px-8 py-8">
                    <div class="grid gap-6">
                        <!-- Row 1 -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Supplier Company <span class="text-red-500">*</span></label>
                                <input type="text" name="supplier_company" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('supplier_company') border-red-500 @enderror" value="{{ old('supplier_company') }}" required>
                                @error('supplier_company')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Supplier Name <span class="text-red-500">*</span></label>
                                <input type="text" name="supplier_name" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('supplier_name') border-red-500 @enderror" value="{{ old('supplier_name') }}" required>
                                @error('supplier_name')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Driver <span class="text-red-500">*</span></label>
                                <input type="text" name="driver" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('driver') border-red-500 @enderror" value="{{ old('driver') }}" required>
                                @error('driver')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Plate Number</label>
                                <input type="text" name="plate_number" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('plate_number') border-red-500 @enderror" value="{{ old('plate_number') }}">
                                @error('plate_number')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Delivery Date <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="delivery_date" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('delivery_date') border-red-500 @enderror" value="{{ old('delivery_date', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('delivery_date')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="border-t border-slate-200 pt-6">
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-slate-900">Fuel Type <span class="text-red-500">*</span></label>
                                <select name="fuel_id" id="fuelType" class="mt-3 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('fuel_id') border-red-500 @enderror" required>
                                    <option value="">Select fuel</option>
                                    @foreach($fuels as $fuel)
                                        <option value="{{ $fuel->id }}" {{ old('fuel_id') == $fuel->id ? 'selected' : '' }}>{{ $fuel->name }}</option>
                                    @endforeach
                                </select>
                                @error('fuel_id')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <!-- Row 4 -->
                        <div class="grid gap-6 lg:grid-cols-3">
                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Quantity (L) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.001" min="0" name="quantity" id="deliveryQuantity" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('quantity') border-red-500 @enderror" value="{{ old('quantity') }}" required>
                                @error('quantity')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Unit Cost (₱) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" name="unit_cost" id="deliveryUnitCost" class="mt-3 w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 transition-colors hover:border-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100 @error('unit_cost') border-red-500 @enderror" value="{{ old('unit_cost') }}" required>
                                @error('unit_cost')<span class="mt-2 block text-sm text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-900">Total Cost (₱)</label>
                                <input type="text" id="deliveryTotalCost" class="mt-3 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 font-bold text-slate-900" value="{{ old('quantity') && old('unit_cost') ? number_format(old('quantity') * old('unit_cost'), 2) : '0.00' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 bg-slate-50 px-8 py-6">
                    <button type="button" class="rounded-full border border-slate-300 bg-white px-6 py-2.5 font-medium text-slate-900 transition-colors hover:bg-slate-100" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="rounded-full bg-red-700 px-8 py-2.5 font-medium text-white transition-all hover:bg-red-800 active:scale-95">Add Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const quantityInput = document.getElementById('deliveryQuantity');
    const unitCostInput = document.getElementById('deliveryUnitCost');
    const totalCostInput = document.getElementById('deliveryTotalCost');

    function updateTotalCost() {
        const quantity = parseFloat(quantityInput?.value || 0);
        const unitCost = parseFloat(unitCostInput?.value || 0);
        totalCostInput.value = (quantity * unitCost).toFixed(2);
    }

    if (quantityInput && unitCostInput) {
        quantityInput.addEventListener('input', updateTotalCost);
        unitCostInput.addEventListener('input', updateTotalCost);
    }
</script>
@endsection
