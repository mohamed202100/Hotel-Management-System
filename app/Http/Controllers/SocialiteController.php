<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub and log the user in.
     */
    public function handleProviderCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors('GitHub authentication failed.');
        }

        // 1. Check if the user already exists by GitHub ID
        $user = User::where('github_id', $githubUser->id)->first();

        // 2. If no user found by GitHub ID, check by email
        if (!$user) {
            $user = User::where('email', $githubUser->email)->first();
        }

        // 3. User found or not found: Login or Create
        if ($user) {
            // Existing user: Update GitHub ID and login
            if (!$user->github_id) {
                $user->update(['github_id' => $githubUser->id]);
            }
        } else {
            // New user: Create User and Customer profiles
            DB::beginTransaction();
            try {
                $user = User::create([
                    'name' => $githubUser->name ?? $githubUser->nickname,
                    'email' => $githubUser->email,
                    'github_id' => $githubUser->id,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)), // Generate a random password
                ]);

                // Assign the default 'guest' role
                $user->assignRole('guest');

                // Create the associated Customer profile (Crucial for booking logic)
                Customer::create([
                    'user_id' => $user->id,
                    'first_name' => $user->name,
                    'last_name' => '',
                    'email' => $user->email,
                    'phone_number' => null,
                    'passport_id' => null,
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Illuminate\Support\Facades\Log::error("Socialite registration failed: " . $e->getMessage());
                return redirect('/login')->withErrors('Failed to create user account. Please try standard registration.');
            }
        }

        Auth::login($user, true);
        return redirect()->route('dashboard');
    }
}
