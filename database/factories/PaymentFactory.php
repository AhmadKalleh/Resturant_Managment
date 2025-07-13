<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::inRandomOrder()->value('id'),
            'amount' => function (array $attributes) {
                $order = \App\Models\Order::with('reservation.table')->find($attributes['order_id']);

                if ($order && $order->reservation && $order->reservation->table) {
                    return $order->total_amount + $order->reservation->table->price;
                }

                return fake()->randomFloat(2, 10, 200); // fallback
            },
            'created_at' => function (array $attributes) {
                $order = \App\Models\Order::with('reservation')->find($attributes['order_id']);

                if ($order && $order->reservation) {
                    return $order->reservation->created_at;
                }

                // fallback في حال لم يوجد الحجز لأي سبب
                return now();
            },

        ];
    }
}
