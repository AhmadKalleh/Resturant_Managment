<?php

namespace Database\Seeders;

use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ResSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حجز منتهي (من يومين إلى يوم أمس)
        Reservation::create([
            'customer_id' => 4,
            'table_id' => 1,
            'reservation_start_time' => Carbon::now('Asia/Damascus')->subDays(2),
            'reservation_end_time' => Carbon::now('Asia/Damascus')->subDays(2)->subMinutes(30),
            'is_checked_in' => true,
        ]);

        Reservation::create([
            'customer_id' => 4,
            'table_id' => 1,
            'reservation_start_time' => Carbon::now('Asia/Damascus')->subDays(3),
            'reservation_end_time' => Carbon::now('Asia/Damascus')->subDays(3)->subMinutes(30),
            'is_checked_in' => true,
        ]);

        // حجز قادم (من الغد إلى بعد غد)
        Reservation::create([
            'customer_id' => 4,
            'table_id' => 3,
            'reservation_start_time' => now()->addHours(3)->subMinutes(31),
            'reservation_end_time' => now()->addHours(3)->addHours(3),
            'is_checked_in' => false,
            'is_extended_delay' => false
        ]);

        Reservation::create([
            'customer_id' => 4,
            'table_id' => 4,
            'reservation_start_time' => now()->addHours(3)->subMinutes(31),
            'reservation_end_time' => now()->addHours(3)->addHours(3),
            'is_checked_in' => false,
            'is_extended_delay' => false
        ]);

        Reservation::create([
            'customer_id' => 4,
            'table_id' => 2,
            'reservation_start_time' => now()->addHours(3)->subMinutes(46),
            'reservation_end_time' => now()->addHours(3)->addHours(3),
            'is_checked_in' => false,
            'is_extended_delay' => true
        ]);
    }
}
