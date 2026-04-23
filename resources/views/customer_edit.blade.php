@extends('layouts.app')

@section('title', 'Edit Customer')
@section('subtitle', 'Update customer information')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Edit Customer</h5>
        <form method="POST" action="{{ route('customer.update', $customer) }}">
            @csrf
            @method('PATCH')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $customer->first_name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $customer->middle_name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $customer->last_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $customer->contact_number) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" required>{{ old('address', $customer->address) }}</textarea>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Customer</button>
            <a href="{{ route('customers') }}" class="btn btn-secondary mt-3">Cancel</a>
        </form>
    </div>
</div>
@endsection