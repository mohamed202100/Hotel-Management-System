<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = $this->faker->dateTimeBetween('now', '+1 week');
        $checkOutDate = $this->faker->dateTimeBetween($checkInDate, $checkInDate->format('Y-m-d') . '+5 days');

        $subtotal = $this->faker->randomFloat(2, 500, 3000);
        $totalAmount = $subtotal * 1.15;

        return [
            'room_id' => Room::inRandomOrder()->first()->id,
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkOutDate->format('Y-m-d'),
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'checked_in']),
        ];
    }
}
