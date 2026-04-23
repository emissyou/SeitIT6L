<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryRequest;
use App\Models\Delivery;
use App\Models\Fuel;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class WetstockController extends Controller
{
    public function index()
    {
        $stockSummary = [];
        try {
            $stockSummary = DB::select('CALL sp_get_current_stock_summary()');
        } catch (\Throwable $e) {
            // Build summary manually if stored procedure fails
            $stockSummary = Fuel::with('inventory')
                ->get()
                ->map(fn ($fuel) => (object) [
                    'fuel_id' => $fuel->id,
                    'fuel_name' => $fuel->name,
                    'current_stock' => $fuel->inventory?->current_stock ?? 0,
                    'capacity' => $fuel->inventory?->capacity ?? 0,
                    'price_per_liter' => $fuel->price_per_liter ?? 0,
                    'percent_full' => $fuel->inventory ? (($fuel->inventory->current_stock / $fuel->inventory->capacity) * 100) : 0,
                    'remaining' => $fuel->inventory ? ($fuel->inventory->capacity - $fuel->inventory->current_stock) : 0,
                ])
                ->toArray();
        }

        // Ensure all required fields exist and get last delivery
        $stockSummary = array_map(function ($stock) {
            // Add missing fields
            if (! isset($stock->remaining) || is_null($stock->remaining)) {
                $stock->remaining = max(0, ($stock->capacity ?? 0) - ($stock->current_stock ?? 0));
            }

            if (! isset($stock->percent_full) || is_null($stock->percent_full)) {
                $capacity = $stock->capacity ?? 0;
                $currentStock = $stock->current_stock ?? 0;
                $stock->percent_full = $capacity > 0 ? (($currentStock / $capacity) * 100) : 0;
            }

            if (! isset($stock->price_per_liter) || is_null($stock->price_per_liter)) {
                $fuel = Fuel::find($stock->fuel_id);
                $stock->price_per_liter = $fuel?->price_per_liter ?? 0;
            }

            // Get last delivery date for this fuel
            $lastDelivery = DB::table('delivery_details')
                ->join('deliveries', 'delivery_details.delivery_id', '=', 'deliveries.id')
                ->where('fuel_id', $stock->fuel_id)
                ->latest('deliveries.delivery_date')
                ->first();

            $stock->last_delivery_date = $lastDelivery?->delivery_date ?? null;

            return $stock;
        }, $stockSummary);

        $deliveries = Delivery::with(['supplier', 'details.fuel'])
            ->latest('delivery_date')
            ->take(12)
            ->get();

        $suppliers = Supplier::orderBy('company_name')->get();
        $fuels = Fuel::orderBy('name')->get();

        return view('wetstock', [
            'stockSummary' => $stockSummary,
            'deliveries' => $deliveries,
            'suppliers' => $suppliers,
            'fuels' => $fuels,
        ]);
    }

    public function store(StoreDeliveryRequest $request)
    {
        $data = $request->validated();

        $totalCost = round($data['quantity'] * $data['unit_cost'], 2);

        DB::transaction(function () use ($data, $totalCost) {
            $delivery = Delivery::create([
                'supplier_id' => $data['supplier_id'],
                'driver' => $data['driver'],
                'plate_number' => $data['plate_number'] ?? null,
                'delivery_date' => $data['delivery_date'],
                'total_cost' => $totalCost,
            ]);

            $delivery->details()->create([
                'fuel_id' => $data['fuel_id'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'],
                'subtotal' => $totalCost,
            ]);
        });

        return redirect()->route('wetstock')->with('success', 'Delivery added successfully.');
    }
}
