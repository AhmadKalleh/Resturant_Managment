<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = \App\Models\Reservation::class;

    public function definition(): array
    {
        $customerIds = Customer::pluck('id')->toArray();
        $tableIds = Table::pluck('id')->toArray();

        // start time يكون تاريخ عشوائي بين سنة قبل اليوم واليوم
        $startTime = $this->faker->dateTimeBetween('-1 years', '-1 day');

        // end time بعد ساعة إلى ساعتين من start time
        $endTime = (clone $startTime)->modify('+'. $this->faker->numberBetween(2, 3) .' hours');

        $customerId = $this->faker->randomElement($customerIds);

        $createdAt = (clone $startTime)->modify('-' . $this->faker->numberBetween(2, 3) . ' hours');

        $isCanceled = false;

        return [
            'customer_id' => $customerId,
            'table_id' => $this->faker->randomElement($tableIds),
            'reservation_start_time' => $startTime,
            'reservation_end_time' => $endTime,
            'is_checked_in' => true,
            'is_canceled' => $isCanceled,
            'canceled_by' => null,
            'is_extended_delay' => false,
            'created_at' => $createdAt,
        ];
    }
}
