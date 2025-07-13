<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = \App\Models\Customer::class;

    public function definition(): array
    {
        return [
            'person_height' => $this->faker->numberBetween(140, 210), // سم
            'person_weight' => $this->faker->numberBetween(40, 150), // كجم
            'blocked_until' => null,
            'no_show_count' => 0,
            'block_reservation' => false,
        ];
    }
}
