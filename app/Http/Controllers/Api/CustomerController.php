<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::role('guest')->get();
        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('guest');
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $customer = User::role('guest')->findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = User::role('guest')->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string',
            'email' => "sometimes|email|unique:users,email,{$id}",
            'password' => 'sometimes|string|min:6',
        ]);

        $customer->update([
            'name' => $request->name ?? $customer->name,
            'email' => $request->email ?? $customer->email,
            'password' => $request->password ? Hash::make($request->password) : $customer->password,
        ]);

        return response()->json($customer);
    }

    public function destroy($id)
    {
        $customer = User::role('guest')->findOrFail($id);
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
