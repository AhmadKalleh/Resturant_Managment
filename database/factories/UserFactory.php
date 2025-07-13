<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = \App\Models\User::class;
    
    public function definition(): array
    {
        $preferred_language = $this->faker->randomElement(['ar', 'en']);

        // اختيار ثيم مفضل عشوائي
        $preferred_theme = $this->faker->randomElement(['dark', 'light']);

        // اختيار الجنس عشوائيا
        $gender = $this->faker->randomElement(['male', 'female']);

        // أسماء حسب الجنس
        $first_name = $gender == 'male' ? $this->faker->firstNameMale() : $this->faker->firstNameFemale();

        // تاريخ الميلاد بصيغة YYYY-MM-DD بين 1950 و 2010
        $date_of_birth = $this->faker->dateTimeBetween('-30 years', '-13 years')->format('Y-m-d');

        return [
            'preferred_language' => $preferred_language,
            'preferred_theme' => $preferred_theme,
            'first_name' => $first_name,
            'last_name' => $this->faker->lastName(),
            'mobile' => '+963' . $this->faker->unique()->numerify('#########'),  // رقم موبايل بصيغة +2010xxx
            'email' => $this->faker->unique()->safeEmail(),
            'fcm-token' => null,
            'gendor' => $gender,
            'date_of_birth' => $date_of_birth,
            'password' => bcrypt('password123'), // كلمة مرور مشفرة
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
