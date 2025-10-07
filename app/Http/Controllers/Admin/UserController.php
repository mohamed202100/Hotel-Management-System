<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role; // Used for assigning roles

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        // Fetch all users except the currently logged-in user (for security)
        $users = User::where('id', '!=', Auth::id())->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        // Roles are not needed here since we default to 'admin'
        return view('admin.users.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            // 'role' field removed from validation
        ]);

        // 1. Create the User record
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 2. Assign the 'admin' role automatically
        $user->assignRole('admin');

        // 3. DO NOT CREATE Customer PROFILE - Only a system user is needed.

        return redirect()->route('users.index')->with('success', 'New Admin user (' . $user->name . ') created successfully.');
    }

    /**
     * Show the form for editing the specified user's role.
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name'); // Get all available roles (Admin, Guest)
        $userRole = $user->roles->pluck('name')->first(); // Get the current role

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user's role.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // Sync the role (removes old role and assigns the new one)
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('success', 'User role updated to ' . $validated['role']);
    }
}
