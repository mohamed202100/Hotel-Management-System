<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 🧩 1. Validate incoming data
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 🧩 2. Create the user
        $user = User::create([
            'name' => ucfirst($validatedData['name']),
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // 🧩 3. Assign default role
        $user->assignRole('guest');

        // 🧩 4. Create related Customer record
        Customer::create([
            'user_id' => $user->id,
            'first_name' => $validatedData['name'],
            'last_name' => '',
            'email' => $validatedData['email'],
            'phone_number' => null,
            'passport_id' => null,
        ]);

        // 🧩 5. Trigger Registered event (useful for email verification)
        event(new Registered($user));

        // 🧩 6. Log in the new user
        Auth::login($user);

        // 🧩 7. Redirect to welcome page
        return redirect()->route('welcome')->with('success', 'Account created successfully!');
    }
}
