<?php
// content of database/seeders/DatabaseSeeder.php
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder; // Import the new Seeder
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Must run roles seeder first
        $this->call([
            RolesAndPermissionsSeeder::class, // <-- ADD THIS LINE
        ]);

        // 2. Create Admin User (and assign the role)
        $user = User::firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $user->assignRole('admin');

        // Disable the rest of the fake data for manual entry
        // \App\Models\Customer::factory(10)->create();
        // \App\Models\Room::factory(50)->create();
        // \App\Models\Reservation::factory(70)->create();
    }
}
