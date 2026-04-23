@extends('layouts.app')

@section('title', 'Customers')
@section('subtitle', 'Manage customer accounts and credit balances')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4">Customer Credit Overview</h2>
        <p class="text-muted">Review active customers and outstanding balances.</p>
    </div>
    <a href="{{ route('customer.create') }}" class="btn btn-primary">Add Customer</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->first_name }} {{ $customer->middle_name ? $customer->middle_name . ' ' : '' }}{{ $customer->last_name }}</td>
                            <td>{{ $customer->contact_number }}</td>
                            <td>{{ $customer->address }}</td>
                            <td>₱{{ number_format($customer->credits->sum('balance'), 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($customer->archived)
                                            <li><a class="dropdown-item" href="#" onclick="restoreCustomer({{ $customer->id }})">Restore</a></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteCustomer({{ $customer->id }})">Delete</a></li>
                                        @else
                                            <li><a class="dropdown-item" href="{{ route('customer.view', $customer) }}">View</a></li>
                                            <li><a class="dropdown-item" href="{{ route('customer.edit', $customer) }}">Edit</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="archiveCustomer({{ $customer->id }})">Archive</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function archiveCustomer(id) {
        if (confirm('Are you sure you want to archive this customer?')) {
            fetch(`{{ url('/customer') }}/${id}/archive`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error archiving customer');
                }
            });
        }
    }

    function restoreCustomer(id) {
        if (confirm('Are you sure you want to restore this customer?')) {
            fetch(`{{ url('/customer') }}/${id}/restore`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error restoring customer');
                }
            });
        }
    }

    function deleteCustomer(id) {
        if (confirm('Are you sure you want to permanently delete this customer?')) {
            fetch(`{{ url('/customer') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error deleting customer');
                }
            });
        }
    }
</script>
@endsection
