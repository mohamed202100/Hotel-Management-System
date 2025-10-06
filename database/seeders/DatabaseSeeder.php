<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Room::factory(50)->create();

        Customer::factory(100)->create();

        Reservation::factory(70)->create()->each(function ($reservation) {
            Invoice::create([
                'reservation_id' => $reservation->id,
                'amount_due' => $reservation->total_amount,
                'amount_paid' => ($reservation->status == 'confirmed' || $reservation->status == 'checked_in') ? $reservation->total_amount : $reservation->total_amount * 0.25,
                'tax_rate' => 0.15,
                'payment_status' => ($reservation->status == 'confirmed' || $reservation->status == 'checked_in') ? 'paid' : 'partially_paid',
                'paid_at' => now(),
            ]);
        });
    }
}
