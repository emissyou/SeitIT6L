<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Support\Facades\DB;

class WetstockController extends Controller
{
    public function index()
    {
        $stockSummary = [];
        try {
            $stockSummary = DB::select('CALL sp_get_current_stock_summary()');
        } catch (\Throwable $e) {
            $stockSummary = [];
        }

        $deliveries = Delivery::with(['supplier', 'receivedBy'])
            ->latest('delivery_date')
            ->take(12)
            ->get();

        return view('wetstock', [
            'stockSummary' => $stockSummary,
            'deliveries' => $deliveries,
        ]);
    }
}
