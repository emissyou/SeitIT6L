@extends('layouts.app')

@section('title', 'View Shift')
@section('subtitle', 'Shift details for ' . $shift->sales_date->format('Y-m-d'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Shift Details</h5>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Date:</strong> {{ $shift->sales_date->format('Y-m-d') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($shift->status) }}</p>
                <p><strong>Opened At:</strong> {{ $shift->opened_at?->format('Y-m-d H:i') }}</p>
                <p><strong>Closed At:</strong> {{ $shift->closed_at?->format('Y-m-d H:i') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Gross Sales:</strong> ₱{{ number_format($shift->gross_sales, 2) }}</p>
                <p><strong>Total Discount:</strong> ₱{{ number_format($shift->total_discount, 2) }}</p>
                <p><strong>Total Credit:</strong> ₱{{ number_format($shift->total_credit, 2) }}</p>
                <p><strong>Net Sales:</strong> ₱{{ number_format($shift->net_sales, 2) }}</p>
            </div>
        </div>

        <h6 class="mt-4">Discounts</h6>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Fuel</th>
                    <th>Customer</th>
                    <th>Liters</th>
                    <th>Retail Price</th>
                    <th>Discounted Price</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shift->discount_details ?? [] as $discount)
                    <tr>
                        <td>{{ $discount['fuel_id'] ? \App\Models\Fuel::find($discount['fuel_id'])->name : 'N/A' }}</td>
                        <td>{{ $discount['customer_id'] ? \App\Models\Customer::find($discount['customer_id'])->first_name . ' ' . \App\Models\Customer::find($discount['customer_id'])->last_name : 'N/A' }}</td>
                        <td>{{ number_format($discount['liters'], 3) }}</td>
                        <td>₱{{ number_format($discount['retail_price'], 2) }}</td>
                        <td>₱{{ number_format($discount['discounted_price'], 2) }}</td>
                        <td>{{ $discount['description'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No discounts</td></tr>
                @endforelse
            </tbody>
        </table>

        <h6 class="mt-4">Credits</h6>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Discounted</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shift->credit_details ?? [] as $credit)
                    <tr>
                        <td>{{ $credit['customer_id'] ? \App\Models\Customer::find($credit['customer_id'])->first_name . ' ' . \App\Models\Customer::find($credit['customer_id'])->last_name : 'N/A' }}</td>
                        <td>₱{{ number_format($credit['amount'], 2) }}</td>
                        <td>{{ $credit['discounted'] ? 'Yes' : 'No' }}</td>
                        <td>{{ $credit['description'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No credits</td></tr>
                @endforelse
            </tbody>
        </table>

        <a href="{{ route('shift.management') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection