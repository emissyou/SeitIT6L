@extends('layouts.app')

@section('title', 'Edit Shift')
@section('subtitle', 'Edit shift details for ' . $shift->sales_date->format('Y-m-d'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Shift</h5>
        <form method="POST" action="{{ route('shift.update', $shift) }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <h6>Discounts</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fuel Type</th>
                                <th>Customer</th>
                                <th>Liters</th>
                                <th>Retail Price</th>
                                <th>Discounted Price</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="discounts-body">
                            @forelse($shift->discount_details ?? [] as $discount)
                                <tr>
                                    <td><select name="discounts[][fuel_id]" class="form-select">{{ $fuels->map(fn($f) => '<option value="' . $f->id . '"' . ($f->id == $discount['fuel_id'] ? ' selected' : '') . '>' . $f->name . '</option>')->join('') }}</select></td>
                                    <td><select name="discounts[][customer_id]" class="form-select">{{ $customers->map(fn($c) => '<option value="' . $c->id . '"' . ($c->id == $discount['customer_id'] ? ' selected' : '') . '>' . $c->first_name . ' ' . $c->last_name . '</option>')->join('') }}</select></td>
                                    <td><input type="number" step="0.001" name="discounts[][liters]" class="form-control" value="{{ $discount['liters'] }}"></td>
                                    <td><input type="number" step="0.01" name="discounts[][retail_price]" class="form-control" value="{{ $discount['retail_price'] }}"></td>
                                    <td><input type="number" step="0.01" name="discounts[][discounted_price]" class="form-control" value="{{ $discount['discounted_price'] }}"></td>
                                    <td><input type="text" name="discounts[][description]" class="form-control" value="{{ $discount['description'] }}"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">Remove</button></td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addDiscountRow()">Add Discount</button>
            </div>

            <div class="mb-4">
                <h6>Credits</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Credit Amount</th>
                                <th>Discounted</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="credits-body">
                            @forelse($shift->credit_details ?? [] as $credit)
                                <tr>
                                    <td><select name="credits[][customer_id]" class="form-select">{{ $customers->map(fn($c) => '<option value="' . $c->id . '"' . ($c->id == $credit['customer_id'] ? ' selected' : '') . '>' . $c->first_name . ' ' . $c->last_name . '</option>')->join('') }}</select></td>
                                    <td><input type="number" step="0.01" name="credits[][amount]" class="form-control" value="{{ $credit['amount'] }}"></td>
                                    <td class="text-center"><input type="hidden" name="credits[][discounted]" value="0"><input type="checkbox" name="credits[][discounted]" value="1" {{ $credit['discounted'] ? 'checked' : '' }}></td>
                                    <td><input type="text" name="credits[][description]" class="form-control" value="{{ $credit['description'] }}"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">Remove</button></td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addCreditRow()">Add Credit</button>
            </div>

            <button type="submit" class="btn btn-primary">Update Shift</button>
            <a href="{{ route('shift.management') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const customerOptions = `@foreach($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>@endforeach`;
    const fuelOptions = `@foreach($fuels as $fuel)<option value="{{ $fuel->id }}">{{ $fuel->name }}</option>@endforeach`;

    function addDiscountRow() {
        const tbody = document.querySelector('#discounts-body');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><select name="discounts[][fuel_id]" class="form-select">${fuelOptions}</select></td>
            <td><select name="discounts[][customer_id]" class="form-select">${customerOptions}</select></td>
            <td><input type="number" step="0.001" name="discounts[][liters]" class="form-control" placeholder="0.000"></td>
            <td><input type="number" step="0.01" name="discounts[][retail_price]" class="form-control" placeholder="Retail"></td>
            <td><input type="number" step="0.01" name="discounts[][discounted_price]" class="form-control" placeholder="Discounted"></td>
            <td><input type="text" name="discounts[][description]" class="form-control" placeholder="Reason"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">Remove</button></td>
        `;
        tbody.appendChild(row);
    }

    function addCreditRow() {
        const tbody = document.querySelector('#credits-body');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><select name="credits[][customer_id]" class="form-select">${customerOptions}</select></td>
            <td><input type="number" step="0.01" name="credits[][amount]" class="form-control"></td>
            <td class="text-center"><input type="hidden" name="credits[][discounted]" value="0"><input type="checkbox" name="credits[][discounted]" value="1"></td>
            <td><input type="text" name="credits[][description]" class="form-control" placeholder="Reason"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">Remove</button></td>
        `;
        tbody.appendChild(row);
    }
</script>
@endsection