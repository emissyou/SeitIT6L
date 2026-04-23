<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DailySale;
use App\Models\Fuel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->query('view', 'home');
        $dateFrom = $request->query('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $statusFilter = $request->query('status', 'all');
        $archivedFilter = $request->query('archived', 'false');

        $shiftQuery = DailySale::with('details.fuel')
            ->whereBetween('sales_date', [$dateFrom, $dateTo]);

        if ($statusFilter !== 'all') {
            $shiftQuery->where('status', $statusFilter);
        }

        if ($archivedFilter === 'true') {
            $shiftQuery->where('archived', true);
        } else {
            $shiftQuery->where('archived', false);
        }

        $shifts = $shiftQuery->orderByDesc('sales_date')->get();
        $openShifts = DailySale::with('details.fuel')->where('status', 'open')->orderByDesc('sales_date')->get();
        $closedShifts = DailySale::with('details.fuel')->where('status', 'closed')->orderByDesc('sales_date')->get();
        $activeShift = DailySale::where('status', 'open')->latest('sales_date')->first();

        $litersByFuel = DB::table('sale_details')
            ->join('fuels', 'sale_details.fuel_id', '=', 'fuels.id')
            ->join('daily_sales', 'sale_details.daily_sale_id', '=', 'daily_sales.id')
            ->select('fuels.name', DB::raw('SUM(sale_details.quantity) AS liters'), DB::raw('SUM(sale_details.amount) AS value'))
            ->whereBetween('daily_sales.sales_date', [$dateFrom, $dateTo])
            ->when($statusFilter !== 'all', fn ($query) => $query->where('daily_sales.status', $statusFilter))
            ->groupBy('fuels.name')
            ->get();

        $totals = [
            'liters' => $shifts->sum(fn ($shift) => $shift->details->sum('quantity')),
            'gross' => $shifts->sum('gross_sales'),
            'discount' => $shifts->sum('total_discount'),
            'credit' => $shifts->sum('total_credit'),
            'net' => $shifts->sum('net_sales'),
        ];

        $customers = Customer::orderBy('last_name')->get();

        $fuels = Fuel::all();

        return view('shift', compact(
            'view',
            'dateFrom',
            'dateTo',
            'statusFilter',
            'archivedFilter',
            'shifts',
            'openShifts',
            'closedShifts',
            'activeShift',
            'litersByFuel',
            'totals',
            'customers',
            'fuels'
        ));
    }

    public function open(Request $request)
    {
        $validated = $request->validate([
            'pump1_premium' => 'required|numeric|min:0',
            'pump1_diesel' => 'required|numeric|min:0',
            'pump2_regular' => 'required|numeric|min:0',
            'pump2_diesel' => 'required|numeric|min:0',
        ]);

        DailySale::create([
            'sales_date' => now()->toDateString(),
            'gross_sales' => 0,
            'total_discount' => 0,
            'total_credit' => 0,
            'net_sales' => 0,
            'status' => 'open',
            'opened_at' => now(),
            'opening_readings' => [
                'pump1_premium' => $validated['pump1_premium'],
                'pump1_diesel' => $validated['pump1_diesel'],
                'pump2_regular' => $validated['pump2_regular'],
                'pump2_diesel' => $validated['pump2_diesel'],
            ],
        ]);

        return redirect()->route('shift.management', ['view' => 'open'])
            ->with('success', 'Shift opened successfully.');
    }

    public function close(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:daily_sales,id',
            'total_credit' => 'nullable|numeric|min:0',
            'current_cash' => 'nullable|numeric|min:0',
            'pump1_premium' => 'required|numeric|min:0',
            'pump1_diesel' => 'required|numeric|min:0',
            'pump2_regular' => 'required|numeric|min:0',
            'pump2_diesel' => 'required|numeric|min:0',
            'pump1_premium_price' => 'required|numeric|min:0',
            'pump1_diesel_price' => 'required|numeric|min:0',
            'pump2_regular_price' => 'required|numeric|min:0',
            'pump2_diesel_price' => 'required|numeric|min:0',
            'discounts' => 'nullable|array',
            'discounts.*.fuel_id' => 'nullable|exists:fuels,id',
            'discounts.*.customer_id' => 'nullable|exists:customers,id',
            'discounts.*.liters' => 'nullable|numeric|min:0',
            'discounts.*.retail_price' => 'nullable|numeric|min:0',
            'discounts.*.discounted_price' => 'nullable|numeric|min:0',
            'discounts.*.description' => 'nullable|string|max:255',
            'credits' => 'nullable|array',
            'credits.*.customer_id' => 'nullable|exists:customers,id',
            'credits.*.amount' => 'nullable|numeric|min:0',
            'credits.*.description' => 'nullable|string|max:255',
            'credits.*.discounted' => 'nullable|boolean',
        ]);

        $shift = DailySale::findOrFail($validated['shift_id']);
        $opening = $shift->opening_readings ?? [];

        $pump1PremiumLiters = max(0, $validated['pump1_premium'] - ($opening['pump1_premium'] ?? 0));
        $pump1DieselLiters = max(0, $validated['pump1_diesel'] - ($opening['pump1_diesel'] ?? 0));
        $pump2RegularLiters = max(0, $validated['pump2_regular'] - ($opening['pump2_regular'] ?? 0));
        $pump2DieselLiters = max(0, $validated['pump2_diesel'] - ($opening['pump2_diesel'] ?? 0));

        $grossSales = ($pump1PremiumLiters * $validated['pump1_premium_price'])
            + ($pump1DieselLiters * $validated['pump1_diesel_price'])
            + ($pump2RegularLiters * $validated['pump2_regular_price'])
            + ($pump2DieselLiters * $validated['pump2_diesel_price']);

        $discountDetails = collect($validated['discounts'] ?? [])->map(function ($discount) {
            $liters = (float) ($discount['liters'] ?? 0);
            $retail = (float) ($discount['retail_price'] ?? 0);
            $discounted = (float) ($discount['discounted_price'] ?? 0);

            return [
                'fuel_id' => $discount['fuel_id'] ?? null,
                'customer_id' => $discount['customer_id'] ?? null,
                'liters' => $liters,
                'retail_price' => $retail,
                'discounted_price' => $discounted,
                'description' => $discount['description'] ?? null,
                'discount_amount' => max(0, ($retail - $discounted) * $liters),
            ];
        })->toArray();

        $totalDiscount = collect($discountDetails)->sum('discount_amount');

        $creditDetails = collect($validated['credits'] ?? [])->map(function ($credit) {
            return [
                'customer_id' => $credit['customer_id'] ?? null,
                'amount' => (float) ($credit['amount'] ?? 0),
                'discounted' => isset($credit['discounted']) && $credit['discounted'],
                'description' => $credit['description'] ?? null,
            ];
        })->toArray();

        $totalCredit = collect($creditDetails)->sum('amount');

        $shift->update([
            'gross_sales' => $grossSales,
            'total_discount' => $totalDiscount,
            'total_credit' => $totalCredit,
            'net_sales' => max(0, $grossSales - $totalDiscount - $totalCredit),
            'status' => 'closed',
            'closed_at' => now(),
            'closing_readings' => [
                'pump1_premium' => $validated['pump1_premium'],
                'pump1_diesel' => $validated['pump1_diesel'],
                'pump2_regular' => $validated['pump2_regular'],
                'pump2_diesel' => $validated['pump2_diesel'],
                'pump1_premium_price' => $validated['pump1_premium_price'],
                'pump1_diesel_price' => $validated['pump1_diesel_price'],
                'pump2_regular_price' => $validated['pump2_regular_price'],
                'pump2_diesel_price' => $validated['pump2_diesel_price'],
            ],
            'discount_details' => $discountDetails,
            'credit_details' => $creditDetails,
        ]);

        return redirect()->route('shift.management', ['view' => 'home'])
            ->with('success', 'Shift closed successfully.');
    }

    public function view(DailySale $shift)
    {
        $shift->load('details.fuel', 'employee');
        return view('shift_view', compact('shift'));
    }

    public function edit(DailySale $shift)
    {
        $shift->load('details.fuel', 'employee');
        $customers = Customer::orderBy('last_name')->get();
        $fuels = Fuel::all();
        return view('shift_edit', compact('shift', 'customers', 'fuels'));
    }

    public function update(Request $request, DailySale $shift)
    {
        // Similar to close, but for update
        // For simplicity, assume it's like closing but updating
        $validated = $request->validate([
            'total_credit' => 'nullable|numeric|min:0',
            'discounts' => 'nullable|array',
            'discounts.*.fuel_id' => 'nullable|exists:fuels,id',
            'discounts.*.customer_id' => 'nullable|exists:customers,id',
            'discounts.*.liters' => 'nullable|numeric|min:0',
            'discounts.*.retail_price' => 'nullable|numeric|min:0',
            'discounts.*.discounted_price' => 'nullable|numeric|min:0',
            'discounts.*.description' => 'nullable|string|max:255',
            'credits' => 'nullable|array',
            'credits.*.customer_id' => 'nullable|exists:customers,id',
            'credits.*.amount' => 'nullable|numeric|min:0',
            'credits.*.description' => 'nullable|string|max:255',
            'credits.*.discounted' => 'nullable|boolean',
        ]);

        $discountDetails = collect($validated['discounts'] ?? [])->map(function ($discount) {
            $liters = (float) ($discount['liters'] ?? 0);
            $retail = (float) ($discount['retail_price'] ?? 0);
            $discounted = (float) ($discount['discounted_price'] ?? 0);

            return [
                'fuel_id' => $discount['fuel_id'] ?? null,
                'customer_id' => $discount['customer_id'] ?? null,
                'liters' => $liters,
                'retail_price' => $retail,
                'discounted_price' => $discounted,
                'description' => $discount['description'] ?? null,
                'discount_amount' => max(0, ($retail - $discounted) * $liters),
            ];
        })->toArray();

        $totalDiscount = collect($discountDetails)->sum('discount_amount');

        $creditDetails = collect($validated['credits'] ?? [])->map(function ($credit) {
            return [
                'customer_id' => $credit['customer_id'] ?? null,
                'amount' => (float) ($credit['amount'] ?? 0),
                'discounted' => isset($credit['discounted']) && $credit['discounted'],
                'description' => $credit['description'] ?? null,
            ];
        })->toArray();

        $totalCredit = collect($creditDetails)->sum('amount');

        $shift->update([
            'total_discount' => $totalDiscount,
            'total_credit' => $totalCredit,
            'net_sales' => max(0, $shift->gross_sales - $totalDiscount - $totalCredit),
            'discount_details' => $discountDetails,
            'credit_details' => $creditDetails,
        ]);

        return redirect()->route('shift.management', ['view' => 'home'])
            ->with('success', 'Shift updated successfully.');
    }

    public function archive(DailySale $shift)
    {
        $shift->update(['archived' => true]);
        return redirect()->back()->with('success', 'Shift archived successfully.');
    }

    public function restore(DailySale $shift)
    {
        $shift->update(['archived' => false]);
        return redirect()->back()->with('success', 'Shift restored successfully.');
    }

    public function destroy(DailySale $shift)
    {
        if (!$shift->archived) {
            return redirect()->back()->with('error', 'Cannot delete unarchived shift.');
        }
        $shift->delete();
        return redirect()->back()->with('success', 'Shift deleted successfully.');
    }
}
