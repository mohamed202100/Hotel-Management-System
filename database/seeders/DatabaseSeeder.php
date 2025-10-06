<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ----------------------------------------------------
        // 1. Ensure Admin User is created on every fresh install
        // ----------------------------------------------------
        $user = User::firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Assign the 'admin' role
        $role = Role::firstOrCreate(['name' => 'admin']);
        $user->assignRole($role);

        // ----------------------------------------------------
        // 3. Disable Seeding for Rooms/Customers/Reservations
        //    (We will add them manually through the Admin Panel)
        // ----------------------------------------------------
        // App\Models\Room::factory(50)->create();
        // App\Models\Customer::factory(100)->create();
        // App\Models\Reservation::factory(70)->create();
    }
}
