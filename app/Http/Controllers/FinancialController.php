<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\Payment;

class FinancialController extends Controller
{
    public function index()
    {
        $outstanding = Credit::sum('balance');
        $totalPayments = Payment::sum('amount_paid');
        $recentPayments = Payment::with('credit.customer')
            ->latest('payment_date')
            ->take(12)
            ->get();

        return view('financials', [
            'outstanding' => $outstanding,
            'totalPayments' => $totalPayments,
            'recentPayments' => $recentPayments,
        ]);
    }
}
