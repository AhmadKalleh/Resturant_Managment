<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customerId = Customer::inRandomOrder()->value('id');
        $reservationId = Reservation::inRandomOrder()->value('id');

        return [
            'customer_id' => $customerId,
            'reservation_id' => $reservationId,
            'total_amount' => $this->faker->randomFloat(2, 100, 1000), // مبلغ عشوائي بين 50 و 500
        ];
    }
}
