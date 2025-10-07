<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Define validation rules based on the fillable fields.
     */
    protected function validationRules($customerId = null)
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customerId . '|max:255',
            'phone_number' => 'nullable|string|max:20',
            'passport_id' => 'required|string|unique:customers,passport_id,' . $customerId . '|max:50',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        Customer::create($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validatedData = $request->validate($this->validationRules($customer->id));

        $customer->update($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Check for related reservations before deleting
        if ($customer->reservations()->count() > 0) {
            return redirect()->route('customers.index')->with('error', 'Cannot delete customer with active reservations.');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }

    public function editGuestProfile(): View
    {
        // 1. Find the Customer record associated with the current logged-in User
        $customer = Auth::user()->customer;
        // Use the generic customer edit view, passing the specific customer object
        return view('my_reservations.edit-profile', compact('customer'));
    }

    /**
     * Update the logged-in user's customer profile.
     */
    public function updateGuestProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $customer = Auth::user()->customer;

        // Validation rules for the guest profile update
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone_number' => 'required|string|min:10|max:20',
            'passport_id' => 'required|string|max:14|min:14',
        ]);

        // 1. Update the Customer record
        $customer->update($validated);

        // 2. OPTIONAL: Update the primary User record if name/email changed (for consistency)
        if ($customer->user) {
            $customer->user->update([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
            ]);
        }

        return redirect()->route('guest.reservations.index')->with('success', 'Your customer profile has been updated successfully.');
    }
}
