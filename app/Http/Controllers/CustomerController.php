<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Credit;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $archived = $request->query('archived', 'false');
        $query = Customer::with(['credits' => function ($query) {
            $query->where('balance', '>', 0);
        }]);

        if ($archived === 'true') {
            $query->where('archived', true);
        } else {
            $query->where('archived', false);
        }

        $customers = $query->get();

        return view('customers', compact('customers', 'archived'));
    }

    public function create()
    {
        return view('customer_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        Customer::create($validated);

        return redirect()->route('customers')->with('success', 'Customer created successfully.');
    }

    public function view(Customer $customer)
    {
        $customer->load(['credits' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('customer_view', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customer_edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('customers')->with('success', 'Customer updated successfully.');
    }

    public function archive(Customer $customer)
    {
        $customer->update(['archived' => true]);
        return redirect()->back()->with('success', 'Customer archived successfully.');
    }

    public function restore(Customer $customer)
    {
        $customer->update(['archived' => false]);
        return redirect()->back()->with('success', 'Customer restored successfully.');
    }

    public function destroy(Customer $customer)
    {
        if (!$customer->archived) {
            return redirect()->back()->with('error', 'Cannot delete unarchived customer.');
        }
        $customer->delete();
        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }
}
