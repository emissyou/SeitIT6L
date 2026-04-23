<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $stockSummary = [];
        $creditReport = [];
        $shiftSummary = [];

        try {
            $stockSummary = DB::select('CALL sp_get_current_stock_summary()');
            $creditReport = DB::select('CALL sp_get_customer_credit_report()');
            $shiftSummary = DB::select('CALL sp_get_shift_summary(?)', [now()->toDateString()]);
        } catch (\Throwable $e) {
            $stockSummary = [];
            $creditReport = [];
            $shiftSummary = [];
        }

        return view('reports', [
            'stockSummary' => $stockSummary,
            'creditReport' => $creditReport,
            'shiftSummary' => $shiftSummary,
        ]);
    }
}
