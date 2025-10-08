<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index()
    {
        $customers = Customer::paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        Customer::create($request->validated());

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        if ($customer->reservations()->exists()) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Cannot delete customer with active reservations.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Show the edit form for the currently logged-in guest.
     */
    public function editGuestProfile(): View
    {
        $customer = Auth::user()->customer;
        return view('my_reservations.edit-profile', compact('customer'));
    }

    /**
     * Update the profile of the currently logged-in guest.
     */
    public function updateGuestProfile(Request $request)
    {
        $customer = Auth::user()->customer;

        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'email'        => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone_number' => 'required|string|min:10|max:20',
            'passport_id'  => 'required|string|max:14|min:14',
        ]);

        $customer->update($validated);

        // Update the linked User record if exists
        optional($customer->user)->update([
            'name'  => $validated['first_name'] . ' ' . ($validated['last_name'] ?? ''),
            'email' => $validated['email'],
        ]);

        return redirect()->route('guest.reservations.index')
            ->with('success', 'Your customer profile has been updated successfully.');
    }
}
