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
        // حجز منتهي (من يومين إلى يوم أمس)
        Reservation::create([
            'customer_id' => 4,
            'table_id' => 1,
            'reservation_start_time' => Carbon::now('Asia/Damascus')->subDays(2)->subMinutes(30),
            'reservation_end_time' => Carbon::now('Asia/Damascus')->subDays(2),
            'is_checked_in' => true,
        ]);

        // حجز قادم (من الغد إلى بعد غد)
        Reservation::create([
            'customer_id' => 4,
            'table_id' => 3,
            'reservation_start_time' => now()->addHours(3)->copy()->subMinutes(10),
            'reservation_end_time' =>  now()->addHours(4)->copy()->subMinutes(10),
            'is_checked_in' => true,
            'is_extended_delay' => false
        ]);

        Reservation::create([
            'customer_id' => 4,
            'table_id' => 3,
            'reservation_start_time' => now()->addHours(6)->copy()->subMinutes(10),
            'reservation_end_time' =>  now()->addHours(7)->copy()->subMinutes(10),
            'is_checked_in' => false,
            'is_extended_delay' => false
        ]);


        // $reservations = Reservation::whereIn('id', [2, 3])->get(); // أو استخدم where('customer_id', 4)->limit(2)

        // foreach ($reservations as $reservation) {
        //     $baseEnd = Carbon::parse($reservation->reservation_end_time);

        //     // تمديد أول: 30 دقيقة بعد نهاية الحجز
        //     $reservation_exte =  ReservationExtension::create([
        //         'reservation_id' => $reservation->id,
        //         'extended_start' => $baseEnd,
        //         'extended_until' => $baseEnd->copy()->addHour(),
        //     ]);

        //     $reservation->update([
        //         'reservation_end_time' => $reservation_exte->extended_until
        //     ]);
        // }
    }
}
