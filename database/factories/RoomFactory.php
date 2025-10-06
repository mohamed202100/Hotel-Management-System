<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['Single', 'Double', 'Suite', 'Deluxe'];

        return [
            'room_number' => $this->faker->unique()->numberBetween(101, 599),
            'type' => $this->faker->randomElement($types),
            'capacity' => $this->faker->numberBetween(1, 4),
            'base_price' => $this->faker->numberBetween(100, 500) * 10,
            'status' => $this->faker->randomElement(['available', 'occupied']),
        ];
    }
}
