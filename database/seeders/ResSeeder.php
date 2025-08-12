<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\ReservationExtension;
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
        $now = now();

        // 🟢 1) حجز قادم (upcoming)
        Reservation::create([
            'customer_id' => 1,
            'table_id' => 1,
            'reservation_start_time' => $now->copy()->addHours(2),
            'reservation_end_time' => $now->copy()->addHours(3),
            'is_checked_in' => false,
            'is_extended_delay' => false
        ]);

        // 🟡 2) حجز في فترة السماح (waiting) — بدأ قبل 20 دقيقة
        Reservation::create([
            'customer_id' => 2,
            'table_id' => 2,
            'reservation_start_time' => $now->copy()->subMinutes(20),
            'reservation_end_time' => $now->copy()->addMinutes(40),
            'is_checked_in' => false,
            'is_extended_delay' => false
        ]);

        // 🟠 3) حجز في فترة التمديد الإضافية (extended_waiting) — بدأ قبل 35 دقيقة مع تمديد
        Reservation::create([
            'customer_id' => 3,
            'table_id' => 3,
            'reservation_start_time' => $now->copy()->subMinutes(35), // 5 دقائق داخل التمديد
            'reservation_end_time' => $now->copy()->addMinutes(25),
            'is_checked_in' => false,
            'is_extended_delay' => true // طلب تمديد التأخير
        ]);

        // 🔴 4) حجز ملغي تلقائياً (canceled_auto) — بدأ قبل ساعة ولم يتم check-in
        Reservation::create([
            'customer_id' => 4,
            'table_id' => 4,
            'reservation_start_time' => $now->copy()->subMinutes(60),
            'reservation_end_time' => $now->copy()->subMinutes(30),
            'is_checked_in' => false,
            'is_extended_delay' => false
        ]);

        // 🟣 5) حجز مكتمل (تم الحضور سابقاً)
        Reservation::create([
            'customer_id' => 5,
            'table_id' => 5,
            'reservation_start_time' => $now->copy()->subHours(3),
            'reservation_end_time' => $now->copy()->subHours(2),
            'is_checked_in' => true,
            'is_extended_delay' => false
        ]);
    }

}
