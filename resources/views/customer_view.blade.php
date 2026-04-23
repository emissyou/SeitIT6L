@extends('layouts.app')

@section('title', 'View Customer')
@section('subtitle', 'Customer details and credit history')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Customer Information</h5>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> {{ $customer->first_name }} {{ $customer->middle_name ? $customer->middle_name . ' ' : '' }}{{ $customer->last_name }}</p>
                <p><strong>Phone:</strong> {{ $customer->contact_number }}</p>
                <p><strong>Address:</strong> {{ $customer->address }}</p>
                <p><strong>Status:</strong> {{ $customer->is_active ? 'Active' : 'Inactive' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Total Outstanding:</strong> ₱{{ number_format($customer->credits->sum('balance'), 2) }}</p>
            </div>
        </div>

        <h6 class="mt-4">Credit History</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fuel Type</th>
                        <th>Liters</th>
                        <th>Amount</th>
                        <th>Liter Price</th>
                        <th>Status</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->credits as $credit)
                        <tr>
                            <td>{{ $credit->created_at->format('Y-m-d') }}</td>
                            <td>{{ $credit->fuel ? $credit->fuel->name : 'N/A' }}</td>
                            <td>{{ number_format($credit->quantity, 3) }}</td>
                            <td>₱{{ number_format($credit->amount, 2) }}</td>
                            <td>₱{{ $credit->quantity > 0 ? number_format($credit->amount / $credit->quantity, 2) : 'N/A' }}</td>
                            <td>{{ $credit->status }}</td>
                            <td>₱{{ number_format($credit->balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No credit history</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h6 class="mt-4">Record Payment</h6>
        <form method="POST" action="{{ route('customer.payment', $customer) }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Amount Paid</label>
                <input type="number" step="0.01" name="amount_paid" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Payment Date</label>
                <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Notes</label>
                <input type="text" name="notes" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">Record Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection