<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Invoice;
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
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->assignRole($adminRole);


        Room::factory(50)->create();
        Customer::factory(100)->create();

        Reservation::factory(70)->create()->each(function ($reservation) {
            Invoice::create([
                'reservation_id' => $reservation->id,
                'amount_due' => $reservation->total_amount,
                'amount_paid' => $reservation->total_amount,
                'tax_rate' => 0.15,
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
        });
    }
}
