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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Create the User record
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. Assign the default 'guest' role
        $user->assignRole('guest');

        // 3. CREATE the associated Customer profile (CRUCIAL FIX)
        // We MUST explicitly pass the 'user_id' of the newly created User.
        Customer::create([
            'user_id' => Auth::id(), // <-- THIS IS THE FIX
            'first_name' => $request->name,
            'last_name' => '',
            'email' => $request->email,
            'phone_number' => null,
            'passport_id' => null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Uses the route() helper function to redirect to the named 'dashboard' route
        return redirect()->route('welcome');
    }
}
