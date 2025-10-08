<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        if (!$customer) return response()->json(['message' => 'Customer not found'], 404);

        return response()->json($customer);
    }
}
