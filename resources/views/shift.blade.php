@extends('layouts.app')

@section('title', 'Shift Management')
@section('subtitle', 'Manage daily shift operations')

@section('content')
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('shift.management', ['view' => 'home']) }}" class="btn @if($view === 'home') btn-danger text-white @else btn-outline-secondary @endif">Home</a>
    <a href="{{ route('shift.management', ['view' => 'open']) }}" class="btn @if($view === 'open') btn-danger text-white @else btn-outline-secondary @endif">Open Shift</a>
    <a href="{{ route('shift.management', ['view' => 'close']) }}" class="btn @if($view === 'close') btn-danger text-white @else btn-outline-secondary @endif">Close Shift</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($view === 'home')
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Liters Sold</h5>
                    <p class="display-6 fw-bold">{{ number_format($totals['liters'] ?? 0, 3) }} L</p>
                    <p class="text-muted mb-0">Showing {{ $statusFilter === 'all' ? 'all shifts' : ucfirst($statusFilter).' shifts' }} from {{ $dateFrom }} to {{ $dateTo }}.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Sales Summary</h5>
                    <div class="row text-center gx-3">
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="text-muted text-uppercase small">Gross Sales</div>
                            <div class="fs-5 fw-semibold">₱{{ number_format($totals['gross'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="text-muted text-uppercase small">Discount</div>
                            <div class="fs-5 fw-semibold">₱{{ number_format($totals['discount'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="text-muted text-uppercase small">Credit</div>
                            <div class="fs-5 fw-semibold">₱{{ number_format($totals['credit'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted text-uppercase small">Net Sales</div>
                            <div class="fs-5 fw-semibold">₱{{ number_format($totals['net'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('shift.management') }}" class="row g-3 align-items-end">
                <input type="hidden" name="view" value="{{ $view }}">
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" @if($statusFilter === 'all') selected @endif>All</option>
                        <option value="open" @if($statusFilter === 'open') selected @endif>Open</option>
                        <option value="closed" @if($statusFilter === 'closed') selected @endif>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Archived</label>
                    <select name="archived" class="form-select">
                        <option value="false" @if($archivedFilter === 'false') selected @endif>No</option>
                        <option value="true" @if($archivedFilter === 'true') selected @endif>Yes</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter Shifts</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Shift History</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total Liters</th>
                            <th>Gross</th>
                            <th>Discount</th>
                            <th>Credit</th>
                            <th>Net</th>
                            <th>Closed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shifts as $shift)
                            <tr>
                                <td>{{ $shift->sales_date->format('Y-m-d') }}</td>
                                <td><span class="badge bg-{{ $shift->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($shift->status) }}</span></td>
                                <td>{{ number_format($shift->details->sum('quantity'), 3) }} L</td>
                                <td>₱{{ number_format($shift->gross_sales, 2) }}</td>
                                <td>₱{{ number_format($shift->total_discount, 2) }}</td>
                                <td>₱{{ number_format($shift->total_credit, 2) }}</td>
                                <td>₱{{ number_format($shift->net_sales, 2) }}</td>
                                <td>{{ $shift->closed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($shift->archived)
                                                <li><a class="dropdown-item" href="#" onclick="restoreShift({{ $shift->id }})">Restore</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteShift({{ $shift->id }})">Delete</a></li>
                                            @else
                                                <li><a class="dropdown-item" href="{{ route('shift.view', $shift) }}">View</a></li>
                                                <li><a class="dropdown-item" href="{{ route('shift.edit', $shift) }}">Edit</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="archiveShift({{ $shift->id }})">Archive</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No shifts found for this range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@if($view === 'open')
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Opening Totalizer Readings</h5>
            <p class="text-muted">Enter the current totalizer readings from both pumps.</p>
            <form method="POST" action="{{ route('shift.open') }}">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 shadow-sm p-4 h-100">
                            <h6 class="fw-semibold">Pump 1</h6>
                            <div class="mb-3">
                                <label class="form-label">Premium (Liters)</label>
                                <input type="number" step="0.001" name="pump1_premium" class="form-control" value="{{ old('pump1_premium', 0) }}">
                            </div>
                            <div>
                                <label class="form-label">Diesel (Liters)</label>
                                <input type="number" step="0.001" name="pump1_diesel" class="form-control" value="{{ old('pump1_diesel', 0) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 shadow-sm p-4 h-100">
                            <h6 class="fw-semibold">Pump 2</h6>
                            <div class="mb-3">
                                <label class="form-label">Regular (Liters)</label>
                                <input type="number" step="0.001" name="pump2_regular" class="form-control" value="{{ old('pump2_regular', 0) }}">
                            </div>
                            <div>
                                <label class="form-label">Diesel (Liters)</label>
                                <input type="number" step="0.001" name="pump2_diesel" class="form-control" value="{{ old('pump2_diesel', 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-danger btn-lg w-100">Open Shift</button>
            </form>
        </div>
    </div>
@endif

@if($view === 'close')
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Closing Totalizer Readings</h5>
            <p class="text-muted">Enter the closing totalizer readings for this shift.</p>

            @if($activeShift)
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 bg-light p-4 h-100">
                            <h6 class="fw-semibold">Pump 1 Opening</h6>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted">Premium</div>
                                    <div class="fw-semibold">{{ number_format($activeShift->opening_readings['pump1_premium'] ?? 0, 3) }} L</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted">Diesel</div>
                                    <div class="fw-semibold">{{ number_format($activeShift->opening_readings['pump1_diesel'] ?? 0, 3) }} L</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 rounded-4 bg-light p-4 h-100">
                            <h6 class="fw-semibold">Pump 2 Opening</h6>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-muted">Regular</div>
                                    <div class="fw-semibold">{{ number_format($activeShift->opening_readings['pump2_regular'] ?? 0, 3) }} L</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-muted">Diesel</div>
                                    <div class="fw-semibold">{{ number_format($activeShift->opening_readings['pump2_diesel'] ?? 0, 3) }} L</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('shift.close') }}">
                    @csrf
                    <input type="hidden" name="shift_id" value="{{ $activeShift->id }}">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Pump 1 Premium (L)</label>
                        <input type="number" step="0.001" class="form-control" name="pump1_premium" id="pump1_premium" value="{{ old('pump1_premium', 0) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pump 1 Premium Price (₱/L)</label>
                        <input type="number" step="0.01" class="form-control" name="pump1_premium_price" id="pump1_premium_price" value="{{ old('pump1_premium_price', 0) }}" required>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Pump 1 Diesel (L)</label>
                        <input type="number" step="0.001" class="form-control" name="pump1_diesel" id="pump1_diesel" value="{{ old('pump1_diesel', 0) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pump 1 Diesel Price (₱/L)</label>
                        <input type="number" step="0.01" class="form-control" name="pump1_diesel_price" id="pump1_diesel_price" value="{{ old('pump1_diesel_price', 0) }}" required>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Pump 2 Regular (L)</label>
                        <input type="number" step="0.001" class="form-control" name="pump2_regular" id="pump2_regular" value="{{ old('pump2_regular', 0) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pump 2 Regular Price (₱/L)</label>
                        <input type="number" step="0.01" class="form-control" name="pump2_regular_price" id="pump2_regular_price" value="{{ old('pump2_regular_price', 0) }}" required>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Pump 2 Diesel (L)</label>
                        <input type="number" step="0.001" class="form-control" name="pump2_diesel" id="pump2_diesel" value="{{ old('pump2_diesel', 0) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pump 2 Diesel Price (₱/L)</label>
                        <input type="number" step="0.01" class="form-control" name="pump2_diesel_price" id="pump2_diesel_price" value="{{ old('pump2_diesel_price', 0) }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Gross Sales (₱)</label>
                        <input type="number" step="0.01" class="form-control" name="gross_sales" id="gross_sales" value="{{ old('gross_sales', 0) }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Credit (₱)</label>
                        <input type="number" step="0.01" class="form-control" name="total_credit" value="{{ old('total_credit', $totals['credit'] ?? 0) }}">
                    </div>
                    <div class="col-md-2">
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-3">Discounts</h6>
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
                                <tbody id="discounts-body"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addDiscountRow()">Add Discount</button>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-3">Credits</h6>
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
                                <tbody id="credits-body"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" onclick="addCreditRow()">Add Credit</button>
                    </div>

                    <button type="submit" class="btn btn-danger">Close Shift</button>
                </form>
            @else
                <div class="alert alert-warning">No active open shift is available to close. Please open a shift first.</div>
            @endif
        </div>
    </div>
@endif
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

    function getNumber(id) {
        return Number(document.getElementById(id)?.value || 0);
    }

    function recalcGross() {
        const premiumLiters = getNumber('pump1_premium');
        const premiumPrice = getNumber('pump1_premium_price');
        const diesel1Liters = getNumber('pump1_diesel');
        const diesel1Price = getNumber('pump1_diesel_price');
        const regularLiters = getNumber('pump2_regular');
        const regularPrice = getNumber('pump2_regular_price');
        const diesel2Liters = getNumber('pump2_diesel');
        const diesel2Price = getNumber('pump2_diesel_price');

        const total = (premiumLiters * premiumPrice)
            + (diesel1Liters * diesel1Price)
            + (regularLiters * regularPrice)
            + (diesel2Liters * diesel2Price);

        document.getElementById('gross_sales').value = total.toFixed(2);
    }

    ['pump1_premium', 'pump1_premium_price', 'pump1_diesel', 'pump1_diesel_price', 'pump2_regular', 'pump2_regular_price', 'pump2_diesel', 'pump2_diesel_price'].forEach((id) => {
        const field = document.getElementById(id);

        if (field) {
            field.addEventListener('input', recalcGross);
        }
    });

    recalcGross();
</script>

<script>
    function archiveShift(id) {
        if (confirm('Are you sure you want to archive this shift?')) {
            fetch(`{{ url('/shift') }}/${id}/archive`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error archiving shift');
                }
            });
        }
    }

    function restoreShift(id) {
        if (confirm('Are you sure you want to restore this shift?')) {
            fetch(`{{ url('/shift') }}/${id}/restore`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error restoring shift');
                }
            });
        }
    }

    function deleteShift(id) {
        if (confirm('Are you sure you want to permanently delete this shift?')) {
            fetch(`{{ url('/shift') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error deleting shift');
                }
            });
        }
    }
</script>
@endsection
