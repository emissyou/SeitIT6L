<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stockSummary = [];
        try {
            $stockSummary = DB::select('CALL sp_get_current_stock_summary()');
        } catch (\Throwable $e) {
            $stockSummary = [];
        }

        $outstandingCredit = Credit::sum('balance');
        $customerCount = Customer::whereHas('credits', function ($query) {
            $query->where('balance', '>', 0);
        })->count();
        $lastShift = DB::table('daily_sales')->latest('sales_date')->first();

        return view('dashboard', [
            'stockSummary' => $stockSummary,
            'outstandingCredit' => $outstandingCredit,
            'customerCount' => $customerCount,
            'lastShift' => $lastShift,
        ]);
    }
}
